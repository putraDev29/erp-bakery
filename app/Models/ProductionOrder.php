<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'order_number',
        'batch_number',
        'recipe_id',
        'product_id',
        'target_quantity',
        'actual_good_quantity',
        'actual_waste_quantity',
        'actual_sample_quantity',
        'status',
        'planned_date',
        'started_at',
        'completed_at',
        'posted_at',
        'material_cost',
        'labor_cost',
        'overhead_cost',
        'total_cost',
        'unit_cost',
        'notes',
        'pic_user_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'target_quantity' => 'decimal:4',
        'actual_good_quantity' => 'decimal:4',
        'actual_waste_quantity' => 'decimal:4',
        'actual_sample_quantity' => 'decimal:4',
        'planned_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'posted_at' => 'datetime',
        'material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(ProductionOutput::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ProductionCost::class);
    }

    public function wastes(): HasMany
    {
        return $this->hasMany(ProductionWaste::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }
}
