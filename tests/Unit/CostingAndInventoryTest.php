<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Ingredient;
use App\Models\StockBalance;
use App\Services\CostingService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CostingAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_moving_weighted_average_formula_is_exact(): void
    {
        $costingService = new CostingService();

        // Old: 10 kg @ Rp 10.000 = Rp 100.000
        // New In: 5 kg @ Rp 16.000 = Rp 80.000
        // Total: 15 kg @ (180.000 / 15) = Rp 12.000
        $newAvg = $costingService->computeNewMovingAverage(10, 10000, 5, 16000);
        $this->assertEquals(12000.00, $newAvg);

        // Production unit HPP test: (100.000 materials + 20.000 labor + 10.000 overhead) / 10 good units = 13.000
        $batchUnitCost = $costingService->computeBatchUnitCost(100000, 20000, 10000, 10);
        $this->assertEquals(13000.00, $batchUnitCost);
    }

    public function test_negative_stock_is_strictly_rejected(): void
    {
        $this->seed();

        $inventoryService = app(InventoryService::class);
        $pusat = Branch::where('code', 'PUSAT')->first();
        $ing = Ingredient::where('code', 'ING-TPG01')->first();

        $balance = StockBalance::where('branch_id', $pusat->id)
            ->where('item_type', 'ingredient')
            ->where('item_id', $ing->id)
            ->first();

        $currentQty = (float) $balance->quantity;

        // Attempting to withdraw more than available must throw RuntimeException
        $this->expectException(RuntimeException::class);
        $inventoryService->decreaseStock(
            $pusat->company_id,
            $pusat->id,
            'ingredient',
            $ing->id,
            $currentQty + 1000,
            'test_out'
        );
    }
}
