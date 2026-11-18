<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'is_central',
        'phone',
        'email',
        'address',
        'manager_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_central' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
