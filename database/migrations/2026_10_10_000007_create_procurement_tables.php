<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->string('purchase_number', 64);
            $table->string('supplier_invoice_no', 64)->nullable();
            $table->date('purchase_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('additional_fee', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->string('status', 32)->default('draft'); // draft, ordered, partially_received, received, cancelled
            $table->string('payment_status', 32)->default('unpaid'); // unpaid, partially_paid, paid, overdue
            $table->string('payment_method', 32)->default('tempo'); // cash, transfer, tempo
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'purchase_number']);
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2);
            $table->decimal('received_qty', 18, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number', 64);
            $table->date('receipt_date');
            $table->text('notes')->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'receipt_number']);
        });

        Schema::create('purchase_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity_received', 18, 4);
            $table->decimal('unit_cost', 18, 2); // allocated cost
            $table->string('batch_number', 64)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('payment_number', 64);
            $table->date('payment_date');
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 32);
            $table->string('reference_number', 64)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'payment_number']);
        });

        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->string('return_number', 64);
            $table->date('return_date');
            $table->decimal('total_amount', 18, 2);
            $table->string('status', 32)->default('posted');
            $table->text('reason')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'return_number']);
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('purchase_payments');
        Schema::dropIfExists('purchase_receipt_items');
        Schema::dropIfExists('purchase_receipts');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
