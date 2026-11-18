<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'ingredient_id',
        'unit_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'subtotal',
        'received_qty',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'received_qty' => 'decimal:4',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
