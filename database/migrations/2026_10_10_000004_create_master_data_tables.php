<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 32); // ingredient, product, expense
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'type']);
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('symbol', 16); // kg, g, ml, l, pcs, box, loyang
            $table->string('type', 32); // mass, volume, count
            $table->timestamps();

            $table->unique(['company_id', 'symbol']);
        });

        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('to_unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('multiplier', 18, 6); // 1 from_unit = multiplier to_unit
            $table->timestamps();

            $table->unique(['company_id', 'from_unit_id', 'to_unit_id']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'code']);
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('purchase_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('min_stock', 18, 4)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0); // standard/last cost
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('barcode', 64)->nullable()->index();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('sale_price', 18, 2)->default(0);
            $table->decimal('min_stock', 18, 4)->default(0);
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('ingredient_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number', 64);
            $table->date('expiry_date')->nullable()->index();
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'ingredient_id', 'batch_number'], 'ing_batch_unique');
        });

        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number', 64);
            $table->date('expiry_date')->nullable()->index();
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id', 'batch_number'], 'prod_batch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
        Schema::dropIfExists('ingredient_batches');
        Schema::dropIfExists('products');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('unit_conversions');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};
