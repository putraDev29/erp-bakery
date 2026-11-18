<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'category_id',
        'unit_id',
        'purchase_unit_id',
        'min_stock',
        'cost_price',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'min_stock' => 'decimal:4',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'item_id')->where('item_type', 'ingredient');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(IngredientBatch::class);
    }
}
