<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    public function journals(Request $request)
    {
        $user = Auth::user();
        $branchId = session('active_branch_id');

        $query = JournalEntry::with(['lines.account', 'branch', 'postedBy'])
            ->where('company_id', $user->company_id)
            ->latest('entry_date');

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('entry_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('entry_date', '<=', $request->end_date);
        }

        $entries = $query->paginate(20);

        return view('accounting.journals', [
            'entries' => $entries,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ]);
    }

    public function chartOfAccounts()
    {
        $user = Auth::user();
        $accounts = ChartOfAccount::where('company_id', $user->company_id)
            ->orderBy('code')
            ->get();

        return view('accounting.coa', [
            'accounts' => $accounts,
        ]);
    }
}
