<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'description',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
