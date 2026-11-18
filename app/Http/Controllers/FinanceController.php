<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\AccountingService;
use App\Services\SequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(
        protected SequenceService $sequenceService,
        protected AccountingService $accountingService
    ) {}

    public function cashIndex()
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $accounts = Account::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        $expenses = Expense::with(['category', 'account', 'branch'])
            ->where('company_id', $user->company_id)
            ->latest('expense_date')
            ->paginate(15);

        $categories = ExpenseCategory::where('company_id', $user->company_id)->get();

        return view('finance.cash', [
            'accounts' => $accounts,
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('active_branch_id');
        $branch = Branch::findOrFail($branchId);
        $user = Auth::user();
        $account = Account::findOrFail($request->account_id);
        $category = ExpenseCategory::with('chartOfAccount')->findOrFail($request->expense_category_id);
        $amount = (float) $request->amount;

        DB::transaction(function () use ($branch, $user, $account, $category, $amount, $request) {
            $expenseNumber = $this->sequenceService->nextNumber($branch, 'KK');

            $expense = Expense::create([
                'company_id' => $branch->company_id,
                'branch_id' => $branch->id,
                'expense_category_id' => $category->id,
                'account_id' => $account->id,
                'expense_number' => $expenseNumber,
                'expense_date' => $request->expense_date,
                'amount' => $amount,
                'notes' => $request->notes,
                'created_by_user_id' => $user->id,
            ]);

            // Decrement account balance
            $account->decrement('current_balance', $amount);

            // Double-entry accounting entry:
            // Debit Expense (6000), Credit Cash/Bank (1110/1120)
            $expenseCoaCode = optional($category->chartOfAccount)->code ?: '6500';
            $cashCoaCode = ($account->type === 'cash') ? '1110' : '1120';

            $journalLines = [
                ['account_code' => $expenseCoaCode, 'debit' => $amount, 'credit' => 0, 'description' => "Beban {$category->name} - {$expense->notes}"],
                ['account_code' => $cashCoaCode, 'debit' => 0, 'credit' => $amount, 'description' => "Pengeluaran Kas/Bank {$account->name}"],
            ];

            $this->accountingService->postJournal(
                $branch->company_id,
                $branch->id,
                $expenseNumber . '-JV',
                $request->expense_date,
                "Jurnal Beban Pengeluaran Kas No. {$expenseNumber}",
                'expense',
                $expense->id,
                $journalLines,
                $user->id
            );
        });

        return redirect()->route('finance.cash')->with('success', 'Biaya pengeluaran operasional berhasil dicatat!');
    }
}
