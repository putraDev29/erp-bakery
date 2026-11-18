<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'item_type',
        'item_id',
        'quantity',
        'avg_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'avg_cost' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function getItemAttribute()
    {
        return $this->item_type === 'ingredient' ? $this->ingredient : $this->product;
    }
}
