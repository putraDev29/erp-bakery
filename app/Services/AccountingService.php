<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountingService
{
    /**
     * Create an immutable double-entry journal record.
     * $lines structure:
     * [
     *    ['account_code' => '1110', 'debit' => 50000, 'credit' => 0, 'description' => '...'],
     *    ['account_code' => '4100', 'debit' => 0, 'credit' => 50000, 'description' => '...'],
     * ]
     */
    public function postJournal(
        int $companyId,
        ?int $branchId,
        string $entryNumber,
        Carbon|string $date,
        string $description,
        string $sourceType,
        ?int $sourceId,
        array $lines,
        ?int $userId = null
    ): JournalEntry {
        $entryDate = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        $totalDebit = 0.0;
        $totalCredit = 0.0;
        $preparedLines = [];

        foreach ($lines as $line) {
            $account = ChartOfAccount::where('company_id', $companyId)
                ->where('code', $line['account_code'])
                ->first();

            if (! $account) {
                throw new InvalidArgumentException("Akun COA dengan kode {$line['account_code']} tidak ditemukan untuk perusahaan ini.");
            }

            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);

            $totalDebit += $debit;
            $totalCredit += $credit;

            $preparedLines[] = [
                'account_id' => $account->id,
                'debit' => $debit,
                'credit' => $credit,
                'description' => $line['description'] ?? $description,
            ];
        }

        // Validate double-entry balance: abs(debit - credit) < 0.01
        if (abs($totalDebit - $totalCredit) >= 0.01) {
            throw new InvalidArgumentException(
                "Jurnal {$entryNumber} tidak seimbang! Total Debit (Rp " . number_format($totalDebit, 2) .
                ") != Total Kredit (Rp " . number_format($totalCredit, 2) . ")"
            );
        }

        return DB::transaction(function () use ($companyId, $branchId, $entryNumber, $entryDate, $sourceType, $sourceId, $description, $userId, $preparedLines) {
            $entry = JournalEntry::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'entry_number' => $entryNumber,
                'entry_date' => $entryDate,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'is_posted' => true,
                'posted_at' => now(),
                'posted_by_user_id' => $userId,
            ]);

            foreach ($preparedLines as $pline) {
                $entry->lines()->create($pline);
            }

            return $entry;
        });
    }

    /**
     * Create reversal journal entry.
     */
    public function reverseJournal(JournalEntry $originalEntry, string $reversalNumber, ?int $userId = null, ?string $reason = null): JournalEntry
    {
        return DB::transaction(function () use ($originalEntry, $reversalNumber, $userId, $reason) {
            $lines = [];
            foreach ($originalEntry->lines as $line) {
                $lines[] = [
                    'account_code' => $line->account->code,
                    'debit' => $line->credit, // Swap debit and credit
                    'credit' => $line->debit,
                    'description' => 'Pembalik: ' . ($line->description ?: $originalEntry->description),
                ];
            }

            $desc = 'Reversal Jurnal ' . $originalEntry->entry_number . ($reason ? " ($reason)" : '');

            return $this->postJournal(
                $originalEntry->company_id,
                $originalEntry->branch_id,
                $reversalNumber,
                now(),
                $desc,
                'reversal',
                $originalEntry->id,
                $lines,
                $userId
            );
        });
    }
}
