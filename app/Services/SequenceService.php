<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BranchSequence;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SequenceService
{
    /**
     * Generate an atomic, race-condition-free document sequence number.
     * Format: {BRANCH_CODE}-{TYPE}-{YYYYMMDD}-{0001}
     */
    public function nextNumber(int|Branch $branch, string $type, ?Carbon $date = null): string
    {
        $branchModel = $branch instanceof Branch ? $branch : Branch::findOrFail($branch);
        $date = $date ?: Carbon::now();
        $period = $date->format('Ymd');

        return DB::transaction(function () use ($branchModel, $type, $period) {
            $seq = BranchSequence::where('branch_id', $branchModel->id)
                ->where('type', strtoupper($type))
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $seq) {
                $seq = BranchSequence::create([
                    'branch_id' => $branchModel->id,
                    'type' => strtoupper($type),
                    'period' => $period,
                    'current_value' => 0,
                ]);
            }

            $seq->increment('current_value');
            $padded = str_pad((string) $seq->current_value, 4, '0', STR_PAD_LEFT);

            return "{$branchModel->code}-" . strtoupper($type) . "-{$period}-{$padded}";
        });
    }
}
