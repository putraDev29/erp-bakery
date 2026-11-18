<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $branchId = $request->has('branch_id') && $request->branch_id !== 'all' ? (int) $request->branch_id : session('active_branch_id');

        $pnl = $this->reportService->getProfitAndLoss($user->company_id, $branchId, $startDate, $endDate);
        $branches = Branch::where('company_id', $user->company_id)->get();
        $comparison = $this->reportService->getBranchComparison($user->company_id, $startDate, $endDate);

        return view('reports.index', [
            'pnl' => $pnl,
            'branches' => $branches,
            'comparison' => $comparison,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedBranchId' => $branchId,
        ]);
    }
}
