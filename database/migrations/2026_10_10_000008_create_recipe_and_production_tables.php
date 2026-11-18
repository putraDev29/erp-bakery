<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('version', 16)->default('1.0');
            $table->decimal('yield_quantity', 18, 4)->default(1);
            $table->foreignId('yield_unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('standard_cost', 18, 2)->default(0);
            $table->decimal('labor_cost', 18, 2)->default(0);
            $table->decimal('overhead_cost', 18, 2)->default(0);
            $table->text('instructions')->nullable();
            $table->integer('prep_time_minutes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'version']);
        });

        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('quantity', 18, 4);
            $table->decimal('standard_cost', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 64);
            $table->string('batch_number', 64);
            $table->foreignId('recipe_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('target_quantity', 18, 4);
            $table->decimal('actual_good_quantity', 18, 4)->default(0);
            $table->decimal('actual_waste_quantity', 18, 4)->default(0);
            $table->decimal('actual_sample_quantity', 18, 4)->default(0);
            $table->string('status', 32)->default('draft'); // draft, planned, in_progress, completed, posted, cancelled
            $table->date('planned_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->decimal('material_cost', 18, 2)->default(0);
            $table->decimal('labor_cost', 18, 2)->default(0);
            $table->decimal('overhead_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'order_number']);
        });

        Schema::create('production_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('planned_quantity', 18, 4);
            $table->decimal('actual_quantity', 18, 4);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('production_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('good_quantity', 18, 4);
            $table->decimal('waste_quantity', 18, 4)->default(0);
            $table->decimal('sample_quantity', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->string('batch_number', 64)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('production_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->string('cost_type', 32); // labor, overhead, other
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_wastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 32); // ingredient, product
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 18, 4);
            $table->decimal('cost_amount', 18, 2)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_wastes');
        Schema::dropIfExists('production_costs');
        Schema::dropIfExists('production_outputs');
        Schema::dropIfExists('production_materials');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('recipes');
    }
};
