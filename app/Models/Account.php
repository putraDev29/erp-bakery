<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name',
        'type',
        'account_number',
        'current_balance',
        'chart_of_account_id',
        'is_active',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }
}
