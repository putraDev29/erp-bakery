<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'sale_id',
        'return_number',
        'return_date',
        'total_refund_amount',
        'refund_method',
        'is_restocked',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_refund_amount' => 'decimal:2',
        'is_restocked' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
