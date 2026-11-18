<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class StockTransferService
{
    public function __construct(
        protected SequenceService $sequenceService,
        protected InventoryService $inventoryService,
        protected AccountingService $accountingService
    ) {}

    /**
     * Request an inter-branch stock transfer.
     */
    public function requestTransfer(
        int $fromBranchId,
        int $toBranchId,
        array $items,
        ?string $notes = null,
        ?int $userId = null
    ): StockTransfer {
        if ($fromBranchId === $toBranchId) {
            throw new InvalidArgumentException("Cabang asal dan cabang tujuan tidak boleh sama.");
        }

        if (empty($items)) {
            throw new InvalidArgumentException("Daftar item transfer tidak boleh kosong.");
        }

        $fromBranch = Branch::findOrFail($fromBranchId);
        $toBranch = Branch::findOrFail($toBranchId);

        return DB::transaction(function () use ($fromBranch, $toBranch, $items, $notes, $userId) {
            $transferNumber = $this->sequenceService->nextNumber($fromBranch, 'TR');

            $transfer = StockTransfer::create([
                'company_id' => $fromBranch->company_id,
                'from_branch_id' => $fromBranch->id,
                'to_branch_id' => $toBranch->id,
                'transfer_number' => $transferNumber,
                'transfer_date' => now()->toDateString(),
                'status' => 'pending_approval',
                'notes' => $notes,
                'requested_by_user_id' => $userId,
            ]);

            $totalEstCost = 0.0;
            foreach ($items as $item) {
                $qty = (float) $item['quantity'];
                $itemType = $item['item_type'] ?? 'product';
                $itemId = (int) $item['item_id'];

                $stock = StockBalance::where('branch_id', $fromBranch->id)
                    ->where('item_type', $itemType)
                    ->where('item_id', $itemId)
                    ->first();

                $unitCost = $stock ? (float) $stock->avg_cost : 0.0;
                $lineCost = round($qty * $unitCost, 2);
                $totalEstCost += $lineCost;

                $transfer->items()->create([
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity_shipped' => $qty,
                    'quantity_received' => 0,
                    'unit_cost' => $unitCost,
                ]);
            }

            $transfer->update(['total_cost' => $totalEstCost]);

            return $transfer->load(['fromBranch', 'toBranch', 'items']);
        });
    }

    /**
     * Dispatch / Ship transfer: Deducts from source branch & marks In-Transit.
     */
    public function shipTransfer(int $transferId, ?int $userId = null): StockTransfer
    {
        $transfer = StockTransfer::with('items')->findOrFail($transferId);

        if (in_array($transfer->status, ['shipped', 'received', 'cancelled'])) {
            throw new RuntimeException("Transfer ini sudah berstatus {$transfer->status}.");
        }

        return DB::transaction(function () use ($transfer, $userId) {
            $totalCost = 0.0;

            foreach ($transfer->items as $item) {
                // Decrease from source branch
                $this->inventoryService->decreaseStock(
                    $transfer->company_id,
                    $transfer->from_branch_id,
                    $item->item_type,
                    $item->item_id,
                    (float) $item->quantity_shipped,
                    'transfer_out',
                    'stock_transfers',
                    $transfer->id,
                    "Kirim transfer antarcabang No. {$transfer->transfer_number}",
                    $userId
                );

                $totalCost += round((float) $item->quantity_shipped * (float) $item->unit_cost, 2);
            }

            $transfer->update([
                'status' => 'shipped',
                'shipped_at' => now(),
                'shipped_by_user_id' => $userId,
                'total_cost' => $totalCost,
            ]);

            // In-Transit Accounting Entry:
            // Debit 1149 (Persediaan In-Transit), Kredit 1143 (Persediaan Cabang Asal)
            $journalLines = [
                ['account_code' => '1149', 'debit' => $totalCost, 'credit' => 0, 'description' => "Persediaan Dalam Perjalanan No. {$transfer->transfer_number}"],
                ['account_code' => '1143', 'debit' => 0, 'credit' => $totalCost, 'description' => "Pengeluaran Transfer Cabang Asal No. {$transfer->transfer_number}"],
            ];

            $this->accountingService->postJournal(
                $transfer->company_id,
                $transfer->from_branch_id,
                $transfer->transfer_number . '-SHIP',
                now(),
                "Jurnal Pengiriman Transfer Stok No. {$transfer->transfer_number}",
                'stock_transfer_ship',
                $transfer->id,
                $journalLines,
                $userId
            );

            return $transfer;
        });
    }

    /**
     * Receive transfer at destination branch: Clears In-Transit & increases destination stock.
     */
    public function receiveTransfer(int $transferId, array $receivedItems, ?int $userId = null): StockTransfer
    {
        $transfer = StockTransfer::with('items')->findOrFail($transferId);

        if ($transfer->status !== 'shipped') {
            throw new RuntimeException("Hanya transfer yang telah berstatus 'shipped' yang dapat diterima.");
        }

        return DB::transaction(function () use ($transfer, $receivedItems, $userId) {
            $totalReceivedCost = 0.0;

            foreach ($receivedItems as $rec) {
                $tItem = $transfer->items->where('id', $rec['transfer_item_id'])->first();
                if (! $tItem) {
                    continue;
                }

                $qtyRec = (float) $rec['quantity_received'];
                $tItem->update(['quantity_received' => $qtyRec]);

                if ($qtyRec > 0) {
                    $this->inventoryService->increaseStock(
                        $transfer->company_id,
                        $transfer->to_branch_id,
                        $tItem->item_type,
                        $tItem->item_id,
                        $qtyRec,
                        (float) $tItem->unit_cost,
                        'transfer_in',
                        'stock_transfers',
                        $transfer->id,
                        "Terima transfer antarcabang No. {$transfer->transfer_number}",
                        $userId
                    );

                    $totalReceivedCost += round($qtyRec * (float) $tItem->unit_cost, 2);
                }
            }

            $transfer->update([
                'status' => 'received',
                'received_at' => now(),
                'received_by_user_id' => $userId,
            ]);

            // Clear In-Transit Accounting Entry:
            // Debit 1143 (Persediaan Cabang Tujuan), Kredit 1149 (Persediaan In-Transit)
            $journalLines = [
                ['account_code' => '1143', 'debit' => $totalReceivedCost, 'credit' => 0, 'description' => "Penerimaan Persediaan Cabang Tujuan No. {$transfer->transfer_number}"],
                ['account_code' => '1149', 'debit' => 0, 'credit' => $totalReceivedCost, 'description' => "Kliring Persediaan Dalam Perjalanan No. {$transfer->transfer_number}"],
            ];

            $this->accountingService->postJournal(
                $transfer->company_id,
                $transfer->to_branch_id,
                $transfer->transfer_number . '-REC',
                now(),
                "Jurnal Penerimaan Transfer Stok No. {$transfer->transfer_number}",
                'stock_transfer_receive',
                $transfer->id,
                $journalLines,
                $userId
            );

            return $transfer;
        });
    }
}
