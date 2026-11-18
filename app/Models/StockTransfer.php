<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'from_branch_id',
        'to_branch_id',
        'transfer_number',
        'transfer_date',
        'status',
        'shipped_at',
        'received_at',
        'total_cost',
        'notes',
        'requested_by_user_id',
        'approved_by_user_id',
        'shipped_by_user_id',
        'received_by_user_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function shippedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_by_user_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}
