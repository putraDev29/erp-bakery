<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_users')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string|array $roleName): bool
    {
        $roles = is_array($roleName) ? $roleName : [$roleName];
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->hasRole(['owner', 'admin_pusat'])) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->pluck('name')->contains($permissionName)) {
                return true;
            }
        }

        return false;
    }

    public function canAccessBranch(int $branchId): bool
    {
        if ($this->hasRole(['owner', 'admin_pusat'])) {
            return true;
        }

        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    public function getDefaultBranch(): ?Branch
    {
        $default = $this->branches()->wherePivot('is_default', true)->first();
        if ($default) {
            return $default;
        }

        return $this->branches()->first() ?? Branch::where('company_id', $this->company_id)->first();
    }
}
