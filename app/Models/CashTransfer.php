<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'transfer_number',
        'transfer_date',
        'from_account_id',
        'to_account_id',
        'amount',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
