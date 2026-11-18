<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionWaste extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'item_type',
        'item_id',
        'quantity',
        'cost_amount',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'cost_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
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
