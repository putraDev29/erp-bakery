<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'cost_type',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}
