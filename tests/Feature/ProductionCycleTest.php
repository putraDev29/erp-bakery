<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Recipe;
use App\Models\StockBalance;
use App\Models\User;
use App\Services\ProductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_order_consumes_materials_and_increases_finished_goods(): void
    {
        $this->seed();

        $pusat = Branch::where('code', 'PUSAT')->first();
        $user = User::where('email', 'produksi.pusat@bakeryerp.local')->first();
        $recipe = Recipe::with('items')->first();
        $product = $recipe->product;

        $productionService = app(ProductionService::class);

        // Initial finished goods stock
        $initialProductStock = (float) StockBalance::where('branch_id', $pusat->id)
            ->where('item_type', 'product')
            ->where('item_id', $product->id)
            ->value('quantity');

        // Create Order for 20 units
        $targetQty = 20;
        $order = $productionService->createOrder($pusat->id, $recipe->id, $targetQty, null, 'Test order', $user->id);

        $this->assertEquals('planned', $order->status);

        // Prepare actual materials (using planned quantities)
        $actualMaterials = [];
        foreach ($order->materials as $mat) {
            $actualMaterials[] = [
                'ingredient_id' => $mat->ingredient_id,
                'actual_quantity' => (float) $mat->planned_quantity,
            ];
        }

        // Finish & Post order
        $finishedOrder = $productionService->finishAndPostOrder(
            $order->id,
            $actualMaterials,
            $targetQty,
            0,
            20000, // labor
            10000, // overhead
            null,
            null,
            $user->id
        );

        $this->assertEquals('posted', $finishedOrder->status);
        $this->assertTrue($finishedOrder->unit_cost > 0);

        // Verify product stock increased by 20
        $newProductStock = (float) StockBalance::where('branch_id', $pusat->id)
            ->where('item_type', 'product')
            ->where('item_id', $product->id)
            ->value('quantity');
        $this->assertEquals($initialProductStock + $targetQty, $newProductStock);

        // Verify journal is posted and balanced
        $journal = JournalEntry::where('source_type', 'production')->where('source_id', $order->id)->first();
        $this->assertNotNull($journal);

        $totalDebit = (float) $journal->lines->sum('debit');
        $totalCredit = (float) $journal->lines->sum('credit');
        $this->assertEquals($totalDebit, $totalCredit);
    }
}
