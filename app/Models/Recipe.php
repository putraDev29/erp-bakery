<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'name',
        'version',
        'yield_quantity',
        'yield_unit_id',
        'standard_cost',
        'labor_cost',
        'overhead_cost',
        'instructions',
        'prep_time_minutes',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:4',
        'standard_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function yieldUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'yield_unit_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }
}
