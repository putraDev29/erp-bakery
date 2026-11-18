<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'expense_category_id',
        'account_id',
        'expense_number',
        'expense_date',
        'amount',
        'notes',
        'receipt_path',
        'created_by_user_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
