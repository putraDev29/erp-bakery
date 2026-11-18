<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'entry_number',
        'entry_date',
        'source_type',
        'source_id',
        'description',
        'is_posted',
        'posted_at',
        'posted_by_user_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_user_id');
    }
}
