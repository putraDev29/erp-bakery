<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'item_type',
        'item_id',
        'batch_number',
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity_in',
        'quantity_out',
        'unit_cost',
        'total_cost',
        'balance_after',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'quantity_in' => 'decimal:4',
        'quantity_out' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'balance_after' => 'decimal:4',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
