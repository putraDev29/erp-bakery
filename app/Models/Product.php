<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'sale_price',
        'min_stock',
        'image_path',
        'description',
        'is_active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'min_stock' => 'decimal:4',
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

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function activeRecipe()
    {
        return $this->hasOne(Recipe::class)->where('is_active', true)->latestOfMany();
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'item_id')->where('item_type', 'product');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }
}
