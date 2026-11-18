<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'item_type',
        'item_id',
        'system_qty',
        'physical_qty',
        'difference_qty',
        'unit_cost',
        'adjustment_reason',
    ];

    protected $casts = [
        'system_qty' => 'decimal:4',
        'physical_qty' => 'decimal:4',
        'difference_qty' => 'decimal:4',
        'unit_cost' => 'decimal:2',
    ];

    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
}
