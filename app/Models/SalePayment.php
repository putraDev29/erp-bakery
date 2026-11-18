<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'sale_id',
        'account_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'cashier_user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }
}
