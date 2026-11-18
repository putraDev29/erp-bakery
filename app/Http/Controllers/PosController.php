<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $categories = Category::where('company_id', $user->company_id)
            ->where('type', 'product')
            ->get();

        $customers = Customer::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        $query = Product::with(['unit', 'category'])
            ->where('company_id', $user->company_id)
            ->where('is_active', true);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        $products = $query->get()->map(function ($product) use ($branchId) {
            $stock = StockBalance::where('branch_id', $branchId)
                ->where('item_type', 'product')
                ->where('item_id', $product->id)
                ->first();
            $product->current_stock = $stock ? (float) $stock->quantity : 0.0;
            return $product;
        });

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,qris,tempo',
            'paid_amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('active_branch_id');
        $user = Auth::user();

        try {
            $sale = $this->saleService->processSale(
                $branchId,
                $request->items,
                $request->payment_method,
                (float) $request->paid_amount,
                $request->customer_id ? (int) $request->customer_id : null,
                $request->notes,
                $user->id
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil diproses.',
                    'sale' => $sale,
                    'receipt_url' => route('pos.receipt', $sale->id),
                ]);
            }

            return redirect()->route('pos.receipt', $sale->id)->with('success', 'Transaksi penjualan berhasil diproses!');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt(int $id)
    {
        $sale = Sale::with(['items.product.unit', 'branch.company', 'customer', 'cashier'])->findOrFail($id);

        return view('pos.receipt', [
            'sale' => $sale,
        ]);
    }

    public function sales(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $query = Sale::with(['customer', 'cashier', 'branch'])
            ->where('company_id', $user->company_id)
            ->latest('sale_date');

        if ($branchId && ! $user->hasRole(['owner', 'admin_pusat'])) {
            $query->where('branch_id', $branchId);
        }

        $sales = $query->paginate(15);

        return view('pos.sales_history', [
            'sales' => $sales,
        ]);
    }
}
