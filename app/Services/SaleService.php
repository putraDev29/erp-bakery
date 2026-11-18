<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockBalance;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        protected SequenceService $sequenceService,
        protected InventoryService $inventoryService,
        protected AccountingService $accountingService
    ) {}

    /**
     * Process an atomic POS Sale transaction.
     */
    public function processSale(
        int $branchId,
        array $items,
        string $paymentMethod = 'cash',
        float $paidAmount = 0.0,
        ?int $customerId = null,
        ?string $notes = null,
        ?int $userId = null
    ): Sale {
        if (empty($items)) {
            throw new InvalidArgumentException("Keranjang belanja tidak boleh kosong.");
        }

        $branch = Branch::findOrFail($branchId);

        return DB::transaction(function () use ($branch, $items, $paymentMethod, $paidAmount, $customerId, $notes, $userId) {
            $invoiceNumber = $this->sequenceService->nextNumber($branch, 'PJ');

            $subtotal = 0.0;
            $discountTotal = 0.0;
            $totalCogs = 0.0;
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : (float) $product->sale_price;
                $discount = (float) ($item['discount'] ?? 0);
                $itemSubtotal = round(($qty * $unitPrice) - $discount, 2);

                // Fetch current stock balance to compute COGS
                $stock = StockBalance::where('branch_id', $branch->id)
                    ->where('item_type', 'product')
                    ->where('item_id', $product->id)
                    ->first();

                $unitCogs = $stock ? (float) $stock->avg_cost : 0.0;
                $itemCogs = round($qty * $unitCogs, 2);

                $subtotal += ($qty * $unitPrice);
                $discountTotal += $discount;
                $totalCogs += $itemCogs;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discount,
                    'subtotal' => $itemSubtotal,
                    'unit_cogs' => $unitCogs,
                    'total_cogs' => $itemCogs,
                ];
            }

            $totalAmount = round($subtotal - $discountTotal, 2);
            $paid = round(max(0.0, $paidAmount), 2);
            $change = $paymentMethod === 'cash' ? max(0.0, $paid - $totalAmount) : 0.0;
            $paymentStatus = ($paid >= $totalAmount) ? 'paid' : ($paid > 0 ? 'partially_paid' : 'unpaid');

            // 1. Create Sale Header
            $sale = Sale::create([
                'company_id' => $branch->company_id,
                'branch_id' => $branch->id,
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'tax_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => min($paid, $totalAmount),
                'change_amount' => $change,
                'total_cogs' => $totalCogs,
                'payment_status' => $paymentStatus,
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'notes' => $notes,
                'cashier_user_id' => $userId,
            ]);

            // 2. Create Sale Items and Decrease Stock
            foreach ($preparedItems as $pItem) {
                $sale->items()->create([
                    'product_id' => $pItem['product']->id,
                    'unit_id' => $pItem['product']->unit_id,
                    'quantity' => $pItem['quantity'],
                    'unit_price' => $pItem['unit_price'],
                    'discount_amount' => $pItem['discount_amount'],
                    'subtotal' => $pItem['subtotal'],
                    'unit_cogs' => $pItem['unit_cogs'],
                    'total_cogs' => $pItem['total_cogs'],
                ]);

                // Atomically decrease stock with row locking & negative stock prevention
                $this->inventoryService->decreaseStock(
                    $branch->company_id,
                    $branch->id,
                    'product',
                    $pItem['product']->id,
                    $pItem['quantity'],
                    'sale_out',
                    'sales',
                    $sale->id,
                    "Penjualan POS No. {$invoiceNumber}",
                    $userId
                );
            }

            // 3. Resolve Account and Record Payment
            $account = null;
            if ($paymentMethod === 'cash') {
                $account = Account::where('branch_id', $branch->id)->where('type', 'cash')->first()
                    ?: Account::where('company_id', $branch->company_id)->where('type', 'cash')->first();
            } else {
                $account = Account::where('company_id', $branch->company_id)->where('type', 'bank')->first();
            }

            if ($account && $paid > 0) {
                $actualPaidIntoAccount = min($paid, $totalAmount);
                $account->increment('current_balance', $actualPaidIntoAccount);

                $sale->payments()->create([
                    'company_id' => $branch->company_id,
                    'branch_id' => $branch->id,
                    'account_id' => $account->id,
                    'payment_number' => $invoiceNumber . '-PAY',
                    'payment_date' => now()->toDateString(),
                    'amount' => $actualPaidIntoAccount,
                    'payment_method' => $paymentMethod,
                    'reference_number' => $invoiceNumber,
                    'notes' => 'Pembayaran penjualan kasir',
                    'cashier_user_id' => $userId,
                ]);
            }

            // 4. Automated Double-Entry Accounting Journal
            $journalLines = [];

            if ($paymentStatus === 'paid') {
                // Tunai / Lunas
                $accountCode = ($paymentMethod === 'cash') ? '1110' : '1120';
                $journalLines[] = ['account_code' => $accountCode, 'debit' => $totalAmount, 'credit' => 0, 'description' => 'Penerimaan Kasir Penjualan'];
                $journalLines[] = ['account_code' => '4100', 'debit' => 0, 'credit' => $totalAmount, 'description' => 'Pendapatan Penjualan Produk'];
            } elseif ($paymentStatus === 'partially_paid') {
                $accountCode = ($paymentMethod === 'cash') ? '1110' : '1120';
                $actualCash = min($paid, $totalAmount);
                $remainingAr = $totalAmount - $actualCash;
                $journalLines[] = ['account_code' => $accountCode, 'debit' => $actualCash, 'credit' => 0, 'description' => 'Penerimaan Uang Muka / Sebagian'];
                $journalLines[] = ['account_code' => '1130', 'debit' => $remainingAr, 'credit' => 0, 'description' => 'Piutang Usaha Pelanggan'];
                $journalLines[] = ['account_code' => '4100', 'debit' => 0, 'credit' => $totalAmount, 'description' => 'Pendapatan Penjualan Produk'];
            } else {
                // Full Kredit / Tempo
                $journalLines[] = ['account_code' => '1130', 'debit' => $totalAmount, 'credit' => 0, 'description' => 'Piutang Usaha Penjualan Kredit'];
                $journalLines[] = ['account_code' => '4100', 'debit' => 0, 'credit' => $totalAmount, 'description' => 'Pendapatan Penjualan Produk'];
            }

            // HPP Journal (Debit COGS 5100, Credit Finished Goods 1143)
            if ($totalCogs > 0) {
                $journalLines[] = ['account_code' => '5100', 'debit' => $totalCogs, 'credit' => 0, 'description' => 'Beban Pokok Penjualan (HPP)'];
                $journalLines[] = ['account_code' => '1143', 'debit' => 0, 'credit' => $totalCogs, 'description' => 'Pengeluaran Persediaan Produk Jadi'];
            }

            $this->accountingService->postJournal(
                $branch->company_id,
                $branch->id,
                $invoiceNumber . '-JV',
                now(),
                "Jurnal Penjualan Kasir No. {$invoiceNumber}",
                'sale',
                $sale->id,
                $journalLines,
                $userId
            );

            return $sale->load(['items.product.unit', 'branch', 'customer', 'cashier']);
        });
    }
}
