<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->boolean('is_central')->default(false);
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'user_id']);
        });

        Schema::create('branch_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'key']);
        });

        Schema::create('branch_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32); // PB, PJ, PR, RT, TR, KM, KK
            $table->string('period', 16); // YYYYMMDD or YYYYMM
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'type', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_sequences');
        Schema::dropIfExists('branch_settings');
        Schema::dropIfExists('branch_users');
        Schema::dropIfExists('branches');
    }
};
