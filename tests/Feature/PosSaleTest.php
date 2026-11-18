<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_sale_checkout_reduces_stock_and_posts_balanced_journal(): void
    {
        $this->seed();

        $cbg1 = Branch::where('code', 'CBG001')->first();
        $kasir = User::where('email', 'kasir.cbg1@bakeryerp.local')->first();
        $product = Product::where('code', 'PRD-ROTI-SOBEK')->first();

        $initialStock = (float) StockBalance::where('branch_id', $cbg1->id)
            ->where('item_type', 'product')
            ->where('item_id', $product->id)
            ->value('quantity');

        $initialCash = (float) Account::where('branch_id', $cbg1->id)
            ->where('type', 'cash')
            ->value('current_balance');

        $this->actingAs($kasir)->withSession(['active_branch_id' => $cbg1->id]);

        $saleQty = 3;
        $unitPrice = (float) $product->sale_price;
        $totalAmount = $saleQty * $unitPrice;

        $response = $this->postJson('/pos/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $saleQty,
                ]
            ],
            'payment_method' => 'cash',
            'paid_amount' => $totalAmount,
            'notes' => 'Test POS checkout',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 1. Verify stock is reduced
        $newStock = (float) StockBalance::where('branch_id', $cbg1->id)
            ->where('item_type', 'product')
            ->where('item_id', $product->id)
            ->value('quantity');
        $this->assertEquals($initialStock - $saleQty, $newStock);

        // 2. Verify cash account balance increased
        $newCash = (float) Account::where('branch_id', $cbg1->id)
            ->where('type', 'cash')
            ->value('current_balance');
        $this->assertEquals($initialCash + $totalAmount, $newCash);

        // 3. Verify Sale record exists in DB
        $sale = Sale::latest()->first();
        $this->assertEquals($totalAmount, (float) $sale->total_amount);
        $this->assertEquals('paid', $sale->payment_status);

        // 4. Verify Journal is posted and strictly balanced
        $journal = JournalEntry::where('source_type', 'sale')->where('source_id', $sale->id)->first();
        $this->assertNotNull($journal);

        $totalDebit = (float) $journal->lines->sum('debit');
        $totalCredit = (float) $journal->lines->sum('credit');
        $this->assertEquals($totalDebit, $totalCredit);
        $this->assertTrue($totalDebit > 0);
    }
}
