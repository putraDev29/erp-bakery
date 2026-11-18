<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\JournalLine;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBalance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Compute comprehensive, database-backed dashboard statistics.
     */
    public function getDashboardStats(int $companyId, ?int $branchId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ?: now()->startOfMonth()->toDateString();
        $end = $endDate ?: now()->endOfMonth()->toDateString();
        $today = now()->toDateString();

        // Query sales base
        $salesQuery = Sale::where('company_id', $companyId)->where('status', 'completed');
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }

        $omzetMonth = (float) (clone $salesQuery)->whereBetween('sale_date', [$start, $end])->sum('total_amount');
        $cogsMonth = (float) (clone $salesQuery)->whereBetween('sale_date', [$start, $end])->sum('total_cogs');
        $omzetToday = (float) (clone $salesQuery)->where('sale_date', $today)->sum('total_amount');
        $salesCountMonth = (int) (clone $salesQuery)->whereBetween('sale_date', [$start, $end])->count();

        // Expenses
        $expenseQuery = Expense::where('company_id', $companyId)->whereBetween('expense_date', [$start, $end]);
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $expensesMonth = (float) $expenseQuery->sum('amount');

        $grossProfit = $omzetMonth - $cogsMonth;
        $netProfit = $grossProfit - $expensesMonth;

        // Cash & Bank Balances
        $accountQuery = Account::where('company_id', $companyId)->where('is_active', true);
        if ($branchId) {
            $accountQuery->where('branch_id', $branchId);
        }
        $cashAndBank = (float) $accountQuery->sum('current_balance');

        // Inventory Valuation (Qty * Avg Cost)
        $stockQuery = StockBalance::where('company_id', $companyId);
        if ($branchId) {
            $stockQuery->where('branch_id', $branchId);
        }
        $inventoryValuation = (float) $stockQuery->select(DB::raw('SUM(quantity * avg_cost) as total_val'))->value('total_val');

        // Accounts Payable (Sisa Hutang Supplier)
        $purchaseQuery = Purchase::where('company_id', $companyId)->where('payment_status', '!=', 'paid');
        if ($branchId) {
            $purchaseQuery->where('branch_id', $branchId);
        }
        $accountsPayable = (float) $purchaseQuery->select(DB::raw('SUM(total_amount - paid_amount) as total_due'))->value('total_due');

        // Accounts Receivable (Sisa Piutang Pelanggan)
        $arQuery = Sale::where('company_id', $companyId)->where('payment_status', '!=', 'paid');
        if ($branchId) {
            $arQuery->where('branch_id', $branchId);
        }
        $accountsReceivable = (float) $arQuery->select(DB::raw('SUM(total_amount - paid_amount) as total_ar'))->value('total_ar');

        // Low stock alerts
        $lowStockQuery = StockBalance::with(['ingredient', 'product', 'branch'])
            ->where('company_id', $companyId);
        if ($branchId) {
            $lowStockQuery->where('branch_id', $branchId);
        }
        $lowStocks = $lowStockQuery->get()->filter(function ($sb) {
            $min = $sb->item ? (float) $sb->item->min_stock : 0;
            return $min > 0 && (float) $sb->quantity <= $min;
        })->take(5);

        // Daily sales chart (last 14 days)
        $chartStartDate = now()->subDays(13)->toDateString();
        $chartQuery = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$chartStartDate, $today]);
        if ($branchId) {
            $chartQuery->where('branch_id', $branchId);
        }
        $dailySales = $chartQuery->groupBy('sale_date')
            ->select('sale_date', DB::raw('SUM(total_amount) as total_revenue'), DB::raw('SUM(total_cogs) as total_cogs'))
            ->orderBy('sale_date', 'asc')
            ->get();

        // Top 5 best selling products
        $topProductsQuery = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.company_id', $companyId)
            ->whereBetween('sales.sale_date', [$start, $end]);
        if ($branchId) {
            $topProductsQuery->where('sales.branch_id', $branchId);
        }
        $topProducts = $topProductsQuery->groupBy('products.id', 'products.name')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_qty'), DB::raw('SUM(sale_items.subtotal) as total_revenue'))
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        return [
            'omzet_month' => $omzetMonth,
            'omzet_today' => $omzetToday,
            'cogs_month' => $cogsMonth,
            'gross_profit' => $grossProfit,
            'expenses_month' => $expensesMonth,
            'net_profit' => $netProfit,
            'cash_and_bank' => $cashAndBank,
            'inventory_valuation' => $inventoryValuation,
            'accounts_payable' => $accountsPayable,
            'accounts_receivable' => $accountsReceivable,
            'sales_count_month' => $salesCountMonth,
            'low_stocks' => $lowStocks,
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
        ];
    }

    /**
     * Profit & Loss Report.
     */
    public function getProfitAndLoss(int $companyId, ?int $branchId = null, string $startDate, string $endDate): array
    {
        $salesQuery = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate]);
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }

        $grossSales = (float) (clone $salesQuery)->sum('subtotal');
        $discounts = (float) (clone $salesQuery)->sum('discount_amount');
        $netSales = (float) (clone $salesQuery)->sum('total_amount');
        $cogs = (float) (clone $salesQuery)->sum('total_cogs');
        $grossProfit = $netSales - $cogs;

        // Group expenses by category
        $expenseQuery = Expense::with('category')
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate]);
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }

        $expenses = $expenseQuery->get()
            ->groupBy('expense_category_id')
            ->map(function ($group) {
                return [
                    'category_name' => optional($group->first()->category)->name ?: 'Operasional Lainnya',
                    'total' => (float) $group->sum('amount'),
                ];
            })->values();

        $totalExpenses = (float) $expenses->sum('total');
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'gross_sales' => $grossSales,
            'discounts' => $discounts,
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'gross_margin_pct' => $netSales > 0 ? round(($grossProfit / $netSales) * 100, 2) : 0,
            'net_margin_pct' => $netSales > 0 ? round(($netProfit / $netSales) * 100, 2) : 0,
        ];
    }

    /**
     * Consolidated Multi-Branch Performance Comparison.
     */
    public function getBranchComparison(int $companyId, string $startDate, string $endDate): array
    {
        $branches = Branch::where('company_id', $companyId)->where('is_active', true)->get();
        $comparison = [];

        foreach ($branches as $b) {
            $sales = (float) Sale::where('branch_id', $b->id)->where('status', 'completed')->whereBetween('sale_date', [$startDate, $endDate])->sum('total_amount');
            $cogs = (float) Sale::where('branch_id', $b->id)->where('status', 'completed')->whereBetween('sale_date', [$startDate, $endDate])->sum('total_cogs');
            $exp = (float) Expense::where('branch_id', $b->id)->whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
            $gross = $sales - $cogs;
            $net = $gross - $exp;
            $invVal = (float) StockBalance::where('branch_id', $b->id)->select(DB::raw('SUM(quantity * avg_cost) as val'))->value('val');
            $cash = (float) Account::where('branch_id', $b->id)->sum('current_balance');

            $comparison[] = [
                'branch_id' => $b->id,
                'branch_code' => $b->code,
                'branch_name' => $b->name,
                'is_central' => $b->is_central,
                'sales' => $sales,
                'cogs' => $cogs,
                'gross_profit' => $gross,
                'expenses' => $exp,
                'net_profit' => $net,
                'inventory_valuation' => $invVal,
                'cash_balance' => $cash,
            ];
        }

        return $comparison;
    }
}
