<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Recipe;
use App\Models\StockBalance;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductionService
{
    public function __construct(
        protected SequenceService $sequenceService,
        protected InventoryService $inventoryService,
        protected CostingService $costingService,
        protected AccountingService $accountingService
    ) {}

    /**
     * Calculate required raw materials for a target production quantity.
     */
    public function calculateRequirements(int $branchId, int $recipeId, float $targetQuantity): array
    {
        $recipe = Recipe::with('items.ingredient.unit')->findOrFail($recipeId);
        $yield = (float) $recipe->yield_quantity;
        $scalingFactor = $yield > 0 ? ($targetQuantity / $yield) : 1.0;

        $items = [];
        $totalEstimatedMaterialCost = 0.0;
        $canProduce = true;

        foreach ($recipe->items as $item) {
            $requiredQty = round((float) $item->quantity * $scalingFactor, 4);

            $stock = StockBalance::where('branch_id', $branchId)
                ->where('item_type', 'ingredient')
                ->where('item_id', $item->ingredient_id)
                ->first();

            $availableQty = $stock ? (float) $stock->quantity : 0.0;
            $unitCost = $stock && $stock->avg_cost > 0 ? (float) $stock->avg_cost : (float) $item->ingredient->cost_price;
            $estCost = round($requiredQty * $unitCost, 2);
            $shortage = max(0.0, $requiredQty - $availableQty);

            if ($shortage > 0) {
                $canProduce = false;
            }

            $totalEstimatedMaterialCost += $estCost;

            $items[] = [
                'ingredient_id' => $item->ingredient_id,
                'ingredient_name' => $item->ingredient->name,
                'unit_symbol' => $item->unit->symbol,
                'required_qty' => $requiredQty,
                'available_qty' => $availableQty,
                'shortage_qty' => $shortage,
                'unit_cost' => $unitCost,
                'estimated_cost' => $estCost,
            ];
        }

        $estLabor = round((float) $recipe->labor_cost * $scalingFactor, 2);
        $estOverhead = round((float) $recipe->overhead_cost * $scalingFactor, 2);
        $totalEstBatchCost = $totalEstimatedMaterialCost + $estLabor + $estOverhead;
        $estUnitHpp = $targetQuantity > 0 ? round($totalEstBatchCost / $targetQuantity, 2) : 0.0;

        return [
            'recipe' => $recipe,
            'target_quantity' => $targetQuantity,
            'scaling_factor' => $scalingFactor,
            'can_produce' => $canProduce,
            'materials' => $items,
            'estimated_material_cost' => $totalEstimatedMaterialCost,
            'estimated_labor_cost' => $estLabor,
            'estimated_overhead_cost' => $estOverhead,
            'estimated_batch_cost' => $totalEstBatchCost,
            'estimated_unit_hpp' => $estUnitHpp,
        ];
    }

    /**
     * Create a Production Order plan.
     */
    public function createOrder(
        int $branchId,
        int $recipeId,
        float $targetQuantity,
        ?string $plannedDate = null,
        ?string $notes = null,
        ?int $userId = null
    ): ProductionOrder {
        $branch = Branch::findOrFail($branchId);
        $recipe = Recipe::findOrFail($recipeId);

        return DB::transaction(function () use ($branch, $recipe, $targetQuantity, $plannedDate, $notes, $userId) {
            $orderNumber = $this->sequenceService->nextNumber($branch, 'PR');
            $batchNumber = 'BATCH-' . now()->format('ymd') . '-' . rand(100, 999);

            $calc = $this->calculateRequirements($branch->id, $recipe->id, $targetQuantity);

            $order = ProductionOrder::create([
                'company_id' => $branch->company_id,
                'branch_id' => $branch->id,
                'order_number' => $orderNumber,
                'batch_number' => $batchNumber,
                'recipe_id' => $recipe->id,
                'product_id' => $recipe->product_id,
                'target_quantity' => $targetQuantity,
                'status' => 'planned',
                'planned_date' => $plannedDate ?: now()->toDateString(),
                'material_cost' => $calc['estimated_material_cost'],
                'labor_cost' => $calc['estimated_labor_cost'],
                'overhead_cost' => $calc['estimated_overhead_cost'],
                'total_cost' => $calc['estimated_batch_cost'],
                'unit_cost' => $calc['estimated_unit_hpp'],
                'notes' => $notes,
                'pic_user_id' => $userId,
                'created_by_user_id' => $userId,
            ]);

            foreach ($calc['materials'] as $mat) {
                $order->materials()->create([
                    'ingredient_id' => $mat['ingredient_id'],
                    'unit_id' => Ingredient::find($mat['ingredient_id'])->unit_id,
                    'planned_quantity' => $mat['required_qty'],
                    'actual_quantity' => $mat['required_qty'],
                    'unit_cost' => $mat['unit_cost'],
                    'total_cost' => $mat['estimated_cost'],
                ]);
            }

            return $order->load(['recipe.product', 'materials.ingredient.unit']);
        });
    }

    /**
     * Finish & Post Production Order:
     * 1. Consumes raw materials from inventory (decreases stock).
     * 2. Computes actual HPP per unit based on good outputs.
     * 3. Increases finished product stock in branch.
     * 4. Posts double-entry journal for inventory conversion.
     */
    public function finishAndPostOrder(
        int $orderId,
        array $actualMaterials,
        float $actualGoodQty,
        float $actualWasteQty = 0.0,
        float $laborCost = 0.0,
        float $overheadCost = 0.0,
        ?string $batchNumber = null,
        ?string $expiryDate = null,
        ?int $userId = null
    ): ProductionOrder {
        $order = ProductionOrder::with('recipe.product')->findOrFail($orderId);

        return DB::transaction(function () use ($order, $actualMaterials, $actualGoodQty, $actualWasteQty, $laborCost, $overheadCost, $batchNumber, $expiryDate, $userId) {
            $totalActualMaterialCost = 0.0;
            $batchNo = $batchNumber ?: $order->batch_number;

            // 1. Consume actual materials
            foreach ($actualMaterials as $mat) {
                $ingId = (int) $mat['ingredient_id'];
                $qty = (float) $mat['actual_quantity'];

                $stock = StockBalance::where('branch_id', $order->branch_id)
                    ->where('item_type', 'ingredient')
                    ->where('item_id', $ingId)
                    ->first();

                $unitCost = $stock ? (float) $stock->avg_cost : (float) Ingredient::find($ingId)->cost_price;
                $lineCost = round($qty * $unitCost, 2);
                $totalActualMaterialCost += $lineCost;

                // Decrease raw material stock with pessimistic row locking
                $this->inventoryService->decreaseStock(
                    $order->company_id,
                    $order->branch_id,
                    'ingredient',
                    $ingId,
                    $qty,
                    'production_consume',
                    'production_orders',
                    $order->id,
                    "Pemakaian bahan produksi Order #{$order->order_number}",
                    $userId
                );

                // Update material row
                $order->materials()->where('ingredient_id', $ingId)->update([
                    'actual_quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $lineCost,
                ]);
            }

            // 2. Compute Actual Unit HPP
            $totalBatchCost = $totalActualMaterialCost + $laborCost + $overheadCost;
            $unitCost = $actualGoodQty > 0 ? round($totalBatchCost / $actualGoodQty, 2) : 0.0;

            // 3. Create Production Output & Increase Finished Goods Stock
            $order->outputs()->create([
                'product_id' => $order->product_id,
                'good_quantity' => $actualGoodQty,
                'waste_quantity' => $actualWasteQty,
                'sample_quantity' => 0,
                'unit_cost' => $unitCost,
                'total_cost' => $totalBatchCost,
                'batch_number' => $batchNo,
                'expiry_date' => $expiryDate ?: now()->addDays(4)->toDateString(),
            ]);

            if ($actualGoodQty > 0) {
                $this->inventoryService->increaseStock(
                    $order->company_id,
                    $order->branch_id,
                    'product',
                    $order->product_id,
                    $actualGoodQty,
                    $unitCost,
                    'production_in',
                    'production_orders',
                    $order->id,
                    "Hasil produksi layak jual Order #{$order->order_number}",
                    $userId,
                    $batchNo
                );
            }

            // Update order status
            $order->update([
                'actual_good_quantity' => $actualGoodQty,
                'actual_waste_quantity' => $actualWasteQty,
                'material_cost' => $totalActualMaterialCost,
                'labor_cost' => $laborCost,
                'overhead_cost' => $overheadCost,
                'total_cost' => $totalBatchCost,
                'unit_cost' => $unitCost,
                'status' => 'posted',
                'completed_at' => now(),
                'posted_at' => now(),
            ]);

            // 4. Double-Entry Accounting Journal
            // (Debit Persediaan Barang Jadi 1143, Kredit Persediaan Bahan Baku 1141)
            $journalLines = [
                ['account_code' => '1143', 'debit' => $totalBatchCost, 'credit' => 0, 'description' => "Barang Jadi Hasil Produksi {$order->order_number}"],
                ['account_code' => '1141', 'debit' => 0, 'credit' => $totalActualMaterialCost, 'description' => "Pengeluaran Bahan Baku Produksi {$order->order_number}"],
            ];

            if (($laborCost + $overheadCost) > 0) {
                $otherCosts = $laborCost + $overheadCost;
                $journalLines[] = ['account_code' => '1110', 'debit' => 0, 'credit' => $otherCosts, 'description' => "Biaya Tenaga Kerja & Overhead Produksi {$order->order_number}"];
            }

            $this->accountingService->postJournal(
                $order->company_id,
                $order->branch_id,
                $order->order_number . '-JV',
                now(),
                "Jurnal Produksi Selesai No. {$order->order_number}",
                'production',
                $order->id,
                $journalLines,
                $userId
            );

            return $order->load(['outputs', 'materials.ingredient.unit', 'recipe.product']);
        });
    }
}
