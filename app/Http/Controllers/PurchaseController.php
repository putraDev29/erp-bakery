<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $query = Purchase::with(['supplier', 'branch', 'items.ingredient.unit'])
            ->where('company_id', $user->company_id)
            ->latest('purchase_date');

        if ($branchId && ! $user->hasRole(['owner', 'admin_pusat'])) {
            $query->where('branch_id', $branchId);
        }

        $purchases = $query->paginate(15);
        $accounts = Account::where('company_id', $user->company_id)->where('is_active', true)->get();

        return view('purchases.index', [
            'purchases' => $purchases,
            'accounts' => $accounts,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $suppliers = Supplier::where('company_id', $user->company_id)->where('is_active', true)->get();
        $ingredients = Ingredient::with('unit')->where('company_id', $user->company_id)->where('is_active', true)->get();

        return view('purchases.create', [
            'suppliers' => $suppliers,
            'ingredients' => $ingredients,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|in:tempo,cash,transfer',
            'supplier_invoice_no' => 'nullable|string',
            'due_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('active_branch_id');
        $user = Auth::user();

        try {
            $purchase = $this->purchaseService->createPurchase(
                $branchId,
                (int) $request->supplier_id,
                $request->items,
                $request->payment_method,
                $request->supplier_invoice_no,
                $request->due_date,
                0.0,
                $request->notes,
                $user->id
            );

            return redirect()->route('purchases.index')->with('success', "Order pembelian No. {$purchase->purchase_number} berhasil dibuat.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receiveForm(int $id)
    {
        $purchase = Purchase::with(['supplier', 'branch', 'items.ingredient.unit'])->findOrFail($id);

        return view('purchases.receive', [
            'purchase' => $purchase,
        ]);
    }

    public function receivePost(Request $request, int $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        try {
            $receipt = $this->purchaseService->receiveGoods(
                $id,
                $request->items,
                $request->notes,
                $user->id
            );

            return redirect()->route('purchases.index')->with('success', "Penerimaan No. {$receipt->receipt_number} berhasil dicatat. Stok bahan baku & nilai rata-rata telah diperbarui!");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payPost(Request $request, int $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bank',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        try {
            $payment = $this->purchaseService->paySupplier(
                $id,
                (int) $request->account_id,
                (float) $request->amount,
                $request->payment_method,
                $request->notes,
                $user->id
            );

            return redirect()->route('purchases.index')->with('success', "Pembayaran No. {$payment->payment_number} berhasil dicatat. Hutang supplier telah berkurang!");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
