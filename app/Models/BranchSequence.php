<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'type',
        'period',
        'current_value',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
