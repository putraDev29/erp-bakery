<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $activeBranchId = session('active_branch_id');

        // Allow owner/admin_pusat to switch between specific branch view or consolidated all branches
        $selectedBranchId = $request->has('branch_id')
            ? ($request->branch_id === 'all' ? null : (int) $request->branch_id)
            : $activeBranchId;

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $stats = $this->reportService->getDashboardStats(
            $user->company_id,
            $selectedBranchId,
            $startDate,
            $endDate
        );

        $branches = $user->hasRole(['owner', 'admin_pusat'])
            ? Branch::where('company_id', $user->company_id)->get()
            : $user->branches;

        return view('dashboard', [
            'stats' => $stats,
            'branches' => $branches,
            'selectedBranchId' => $selectedBranchId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
