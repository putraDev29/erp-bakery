<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'ingredient_id',
        'unit_id',
        'planned_quantity',
        'actual_quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
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
