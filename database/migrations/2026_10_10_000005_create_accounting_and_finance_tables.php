<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->string('type', 32); // asset, liability, equity, revenue, cogs, expense
            $table->string('normal_balance', 10); // debit, credit
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entry_number', 64)->index();
            $table->date('entry_date')->index();
            $table->string('source_type', 64)->nullable(); // purchase, sale, production, transfer, expense, cash
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description');
            $table->boolean('is_posted')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 32);
            $table->string('name');
            $table->string('type', 32); // cash, bank
            $table->string('account_number', 64)->nullable();
            $table->decimal('current_balance', 18, 2)->default(0);
            $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('transaction_number', 64);
            $table->date('transaction_date');
            $table->string('type', 16); // in, out
            $table->string('category', 64); // operational, capital, prive, sales_payment, purchase_payment, other
            $table->decimal('amount', 18, 2);
            $table->string('reference_type', 64)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'transaction_number']);
        });

        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('transfer_number', 64)->unique();
            $table->date('transfer_date');
            $table->foreignId('from_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->restrictOnDelete();
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->string('expense_number', 64);
            $table->date('expense_date');
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'expense_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('cash_transfers');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
