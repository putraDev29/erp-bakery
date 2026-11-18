<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number', 64);
            $table->date('sale_date');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('change_amount', 18, 2)->default(0);
            $table->decimal('total_cogs', 18, 2)->default(0);
            $table->string('payment_status', 32)->default('paid'); // paid, partially_paid, unpaid
            $table->string('status', 32)->default('completed'); // draft, completed, cancelled
            $table->string('payment_method', 32)->default('cash'); // cash, transfer, qris, credit
            $table->text('notes')->nullable();
            $table->foreignId('cashier_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'invoice_number']);
            $table->index(['sale_date', 'branch_id']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2);
            $table->decimal('unit_cogs', 18, 2)->default(0);
            $table->decimal('total_cogs', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('payment_number', 64);
            $table->date('payment_date');
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 32);
            $table->string('reference_number', 64)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('cashier_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'payment_number']);
        });

        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('return_number', 64);
            $table->date('return_date');
            $table->decimal('total_refund_amount', 18, 2);
            $table->string('refund_method', 32)->default('cash');
            $table->boolean('is_restocked')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'return_number']);
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('refund_subtotal', 18, 2);
            $table->boolean('is_good_condition')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
