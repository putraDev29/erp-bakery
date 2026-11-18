<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\PurchaseReceipt;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseService
{
    public function __construct(
        protected SequenceService $sequenceService,
        protected InventoryService $inventoryService,
        protected AccountingService $accountingService
    ) {}

    /**
     * Create a Purchase Order.
     */
    public function createPurchase(
        int $branchId,
        int $supplierId,
        array $items,
        string $paymentMethod = 'tempo',
        ?string $supplierInvoiceNo = null,
        ?string $dueDate = null,
        float $additionalFee = 0.0,
        ?string $notes = null,
        ?int $userId = null
    ): Purchase {
        if (empty($items)) {
            throw new InvalidArgumentException("Daftar item pembelian tidak boleh kosong.");
        }

        $branch = Branch::findOrFail($branchId);
        $supplier = Supplier::findOrFail($supplierId);

        return DB::transaction(function () use ($branch, $supplier, $items, $paymentMethod, $supplierInvoiceNo, $dueDate, $additionalFee, $notes, $userId) {
            $purchaseNumber = $this->sequenceService->nextNumber($branch, 'PB');

            $subtotal = 0.0;
            $discountTotal = 0.0;
            $preparedItems = [];

            foreach ($items as $item) {
                $ingredient = Ingredient::findOrFail($item['ingredient_id']);
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $itemSubtotal = round(($qty * $price) - $discount, 2);

                $subtotal += ($qty * $price);
                $discountTotal += $discount;

                $preparedItems[] = [
                    'ingredient_id' => $ingredient->id,
                    'unit_id' => $ingredient->unit_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_amount' => $discount,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $totalAmount = round($subtotal - $discountTotal + $additionalFee, 2);

            $purchase = Purchase::create([
                'company_id' => $branch->company_id,
                'branch_id' => $branch->id,
                'supplier_id' => $supplier->id,
                'purchase_number' => $purchaseNumber,
                'supplier_invoice_no' => $supplierInvoiceNo,
                'purchase_date' => now()->toDateString(),
                'due_date' => $dueDate ?: now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountTotal,
                'tax_amount' => 0,
                'additional_fee' => $additionalFee,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'ordered',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'notes' => $notes,
                'created_by_user_id' => $userId,
            ]);

            foreach ($preparedItems as $pItem) {
                $purchase->items()->create($pItem);
            }

            return $purchase->load(['items.ingredient.unit', 'supplier', 'branch']);
        });
    }

    /**
     * Receive physical goods from purchase.
     * Updates physical stock, recalculates moving average cost, and posts inventory journal.
     */
    public function receiveGoods(
        int $purchaseId,
        array $receivedItems,
        ?string $notes = null,
        ?int $userId = null
    ): PurchaseReceipt {
        $purchase = Purchase::with('items')->findOrFail($purchaseId);

        return DB::transaction(function () use ($purchase, $receivedItems, $notes, $userId) {
            $receiptNumber = $this->sequenceService->nextNumber($purchase->branch, 'RC');

            $receipt = PurchaseReceipt::create([
                'company_id' => $purchase->company_id,
                'branch_id' => $purchase->branch_id,
                'purchase_id' => $purchase->id,
                'receipt_number' => $receiptNumber,
                'receipt_date' => now()->toDateString(),
                'notes' => $notes,
                'received_by_user_id' => $userId,
            ]);

            $totalReceivedValue = 0.0;
            $allReceived = true;

            foreach ($receivedItems as $rec) {
                $pItem = $purchase->items->where('id', $rec['purchase_item_id'])->first();
                if (! $pItem) {
                    continue;
                }

                $qtyReceived = (float) $rec['quantity_received'];
                if ($qtyReceived <= 0) {
                    continue;
                }

                $unitCost = (float) $pItem->unit_price;
                $lineValue = round($qtyReceived * $unitCost, 2);
                $totalReceivedValue += $lineValue;

                $receipt->items()->create([
                    'purchase_item_id' => $pItem->id,
                    'ingredient_id' => $pItem->ingredient_id,
                    'quantity_received' => $qtyReceived,
                    'unit_cost' => $unitCost,
                    'batch_number' => $rec['batch_number'] ?? null,
                    'expiry_date' => $rec['expiry_date'] ?? null,
                ]);

                // Update received quantity on purchase item
                $pItem->increment('received_qty', $qtyReceived);

                if ($pItem->received_qty < $pItem->quantity) {
                    $allReceived = false;
                }

                // Increase physical inventory stock & auto-update moving average!
                $this->inventoryService->increaseStock(
                    $purchase->company_id,
                    $purchase->branch_id,
                    'ingredient',
                    $pItem->ingredient_id,
                    $qtyReceived,
                    $unitCost,
                    'purchase_in',
                    'purchase_receipts',
                    $receipt->id,
                    "Penerimaan Pembelian No. {$receiptNumber}",
                    $userId,
                    $rec['batch_number'] ?? null
                );
            }

            // Update purchase receipt status
            $purchase->update([
                'status' => $allReceived ? 'received' : 'partially_received',
            ]);

            // Post accounting journal for inventory receipt
            $journalLines = [];
            if ($purchase->payment_method === 'cash') {
                $journalLines[] = ['account_code' => '1141', 'debit' => $totalReceivedValue, 'credit' => 0, 'description' => 'Persediaan Bahan Baku Masuk'];
                $journalLines[] = ['account_code' => '1110', 'debit' => 0, 'credit' => $totalReceivedValue, 'description' => 'Pembayaran Tunai Pembelian'];
                $purchase->update(['payment_status' => 'paid', 'paid_amount' => $totalReceivedValue]);
            } else {
                $journalLines[] = ['account_code' => '1141', 'debit' => $totalReceivedValue, 'credit' => 0, 'description' => 'Persediaan Bahan Baku Masuk'];
                $journalLines[] = ['account_code' => '2110', 'debit' => 0, 'credit' => $totalReceivedValue, 'description' => 'Hutang Usaha Supplier'];
            }

            $this->accountingService->postJournal(
                $purchase->company_id,
                $purchase->branch_id,
                $receiptNumber . '-JV',
                now(),
                "Jurnal Penerimaan Pembelian No. {$receiptNumber}",
                'purchase_receipt',
                $receipt->id,
                $journalLines,
                $userId
            );

            return $receipt->load('items.ingredient.unit');
        });
    }

    /**
     * Pay supplier invoice (Accounts Payable settlement).
     */
    public function paySupplier(
        int $purchaseId,
        int $accountId,
        float $amount,
        string $paymentMethod = 'bank',
        ?string $notes = null,
        ?int $userId = null
    ): PurchasePayment {
        $purchase = Purchase::findOrFail($purchaseId);
        $account = Account::findOrFail($accountId);

        return DB::transaction(function () use ($purchase, $account, $amount, $paymentMethod, $notes, $userId) {
            $paymentNumber = $this->sequenceService->nextNumber($purchase->branch, 'KK');

            $payment = PurchasePayment::create([
                'company_id' => $purchase->company_id,
                'branch_id' => $purchase->branch_id,
                'purchase_id' => $purchase->id,
                'account_id' => $account->id,
                'payment_number' => $paymentNumber,
                'payment_date' => now()->toDateString(),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $purchase->purchase_number,
                'notes' => $notes ?: "Pelunasan Hutang Supplier: {$purchase->purchase_number}",
                'created_by_user_id' => $userId,
            ]);

            // Decrement account balance
            $account->decrement('current_balance', $amount);

            // Update purchase paid status
            $newPaid = $purchase->paid_amount + $amount;
            $paymentStatus = ($newPaid >= $purchase->total_amount) ? 'paid' : 'partially_paid';
            $purchase->update([
                'paid_amount' => $newPaid,
                'payment_status' => $paymentStatus,
            ]);

            // Post double-entry journal (Debit AP 2110, Credit Cash/Bank)
            $accountCode = ($account->type === 'cash') ? '1110' : '1120';
            $journalLines = [
                ['account_code' => '2110', 'debit' => $amount, 'credit' => 0, 'description' => "Pelunasan Hutang {$purchase->purchase_number}"],
                ['account_code' => $accountCode, 'debit' => 0, 'credit' => $amount, 'description' => "Pengeluaran Kas/Bank {$account->name}"],
            ];

            $this->accountingService->postJournal(
                $purchase->company_id,
                $purchase->branch_id,
                $paymentNumber . '-JV',
                now(),
                "Jurnal Pembayaran Hutang No. {$paymentNumber}",
                'purchase_payment',
                $payment->id,
                $journalLines,
                $userId
            );

            return $payment;
        });
    }
}
