<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_type', 32); // ingredient, product
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('avg_cost', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'item_type', 'item_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_type', 32); // ingredient, product
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number', 64)->nullable();
            $table->string('movement_type', 32); // purchase_in, production_consume, production_in, sale_out, transfer_out, transfer_in, opname_adjust, return_in, return_out
            $table->string('reference_type', 64)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity_in', 18, 4)->default(0);
            $table->decimal('quantity_out', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->decimal('balance_after', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'item_type', 'item_id']);
            $table->index(['movement_type', 'created_at']);
        });

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('transfer_number', 64)->unique();
            $table->date('transfer_date');
            $table->string('status', 32)->default('draft'); // draft, pending_approval, approved, shipped, partially_received, received, cancelled
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('shipped_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 32); // ingredient, product
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity_shipped', 18, 4);
            $table->decimal('quantity_received', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('opname_number', 64);
            $table->date('opname_date');
            $table->string('status', 32)->default('draft'); // draft, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'opname_number']);
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 32); // ingredient, product
            $table->unsignedBigInteger('item_id');
            $table->decimal('system_qty', 18, 4);
            $table->decimal('physical_qty', 18, 4);
            $table->decimal('difference_qty', 18, 4);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->string('adjustment_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_balances');
    }
};
