<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'product_id',
        'good_quantity',
        'waste_quantity',
        'sample_quantity',
        'unit_cost',
        'total_cost',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'good_quantity' => 'decimal:4',
        'waste_quantity' => 'decimal:4',
        'sample_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
