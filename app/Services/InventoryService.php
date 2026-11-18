<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    public function __construct(
        protected CostingService $costingService
    ) {}

    /**
     * Get or initialize stock balance record.
     */
    public function getBalance(int $branchId, string $itemType, int $itemId): StockBalance
    {
        return StockBalance::firstOrCreate(
            [
                'branch_id' => $branchId,
                'item_type' => $itemType,
                'item_id' => $itemId,
            ],
            [
                'company_id' => $this->resolveCompanyId($itemType, $itemId),
                'quantity' => 0,
                'avg_cost' => 0,
            ]
        );
    }

    /**
     * Increase stock (e.g. purchase receipt, production output, transfer-in).
     * Atomically recalculates moving average cost and appends stock movement ledger.
     */
    public function increaseStock(
        int $companyId,
        int $branchId,
        string $itemType,
        int $itemId,
        float $quantity,
        float $unitCost,
        string $movementType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null,
        ?string $batchNumber = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Kuantitas penambahan stok harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($companyId, $branchId, $itemType, $itemId, $quantity, $unitCost, $movementType, $referenceType, $referenceId, $notes, $userId, $batchNumber) {
            $balance = StockBalance::where('branch_id', $branchId)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            $oldQty = $balance ? (float) $balance->quantity : 0.0;
            $oldAvg = $balance ? (float) $balance->avg_cost : 0.0;

            $newAvg = $this->costingService->computeNewMovingAverage($oldQty, $oldAvg, $quantity, $unitCost);
            $newQty = $oldQty + $quantity;

            if (! $balance) {
                $balance = StockBalance::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity' => $newQty,
                    'avg_cost' => $newAvg,
                ]);
            } else {
                $balance->update([
                    'quantity' => $newQty,
                    'avg_cost' => $newAvg,
                ]);
            }

            return StockMovement::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'batch_number' => $batchNumber,
                'movement_type' => $movementType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'unit_cost' => $unitCost,
                'total_cost' => round($quantity * $unitCost, 2),
                'balance_after' => $newQty,
                'notes' => $notes,
                'created_by_user_id' => $userId,
            ]);
        });
    }

    /**
     * Decrease stock (e.g. sales checkout, production raw consumption, transfer-out).
     * Atomically locks the row and strictly forbids negative stock.
     */
    public function decreaseStock(
        int $companyId,
        int $branchId,
        string $itemType,
        int $itemId,
        float $quantity,
        string $movementType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null,
        ?string $batchNumber = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Kuantitas pengurangan stok harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($companyId, $branchId, $itemType, $itemId, $quantity, $movementType, $referenceType, $referenceId, $notes, $userId, $batchNumber) {
            $balance = StockBalance::where('branch_id', $branchId)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            $currentQty = $balance ? (float) $balance->quantity : 0.0;
            $currentAvg = $balance ? (float) $balance->avg_cost : 0.0;

            if ($currentQty < $quantity) {
                $itemName = $this->resolveItemName($itemType, $itemId);
                throw new RuntimeException(
                    "Stok tidak mencukupi untuk item '{$itemName}' di cabang ini! Tersedia: " .
                    number_format($currentQty, 2) . ", Diminta: " . number_format($quantity, 2)
                );
            }

            $newQty = $currentQty - $quantity;
            $balance->update(['quantity' => $newQty]);

            return StockMovement::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'batch_number' => $batchNumber,
                'movement_type' => $movementType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity_in' => 0,
                'quantity_out' => $quantity,
                'unit_cost' => $currentAvg,
                'total_cost' => round($quantity * $currentAvg, 2),
                'balance_after' => $newQty,
                'notes' => $notes,
                'created_by_user_id' => $userId,
            ]);
        });
    }

    private function resolveCompanyId(string $itemType, int $itemId): int
    {
        if ($itemType === 'ingredient') {
            return Ingredient::findOrFail($itemId)->company_id;
        }
        return Product::findOrFail($itemId)->company_id;
    }

    private function resolveItemName(string $itemType, int $itemId): string
    {
        if ($itemType === 'ingredient') {
            $item = Ingredient::find($itemId);
            return $item ? $item->name : "Ingredient #{$itemId}";
        }
        $item = Product::find($itemId);
        return $item ? $item->name : "Product #{$itemId}";
    }
}
