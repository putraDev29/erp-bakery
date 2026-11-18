<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    public function __construct(
        protected StockTransferService $transferService
    ) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $query = StockTransfer::with(['fromBranch', 'toBranch', 'items'])
            ->where('company_id', $user->company_id)
            ->latest('created_at');

        if (! $user->hasRole(['owner', 'admin_pusat'])) {
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        $transfers = $query->paginate(15);

        return view('transfers.index', [
            'transfers' => $transfers,
        ]);
    }

    public function create()
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $branches = Branch::where('company_id', $user->company_id)
            ->where('id', '!=', $branchId)
            ->where('is_active', true)
            ->get();

        $products = Product::with('unit')->where('company_id', $user->company_id)->get();
        $ingredients = Ingredient::with('unit')->where('company_id', $user->company_id)->get();

        return view('transfers.create', [
            'branches' => $branches,
            'products' => $products,
            'ingredients' => $ingredients,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:ingredient,product',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $fromBranchId = session('active_branch_id');
        $user = Auth::user();

        try {
            $transfer = $this->transferService->requestTransfer(
                $fromBranchId,
                (int) $request->to_branch_id,
                $request->items,
                $request->notes,
                $user->id
            );

            return redirect()->route('transfers.index')->with('success', "Permintaan transfer No. {$transfer->transfer_number} berhasil dibuat.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function ship(int $id)
    {
        $user = Auth::user();

        try {
            $transfer = $this->transferService->shipTransfer($id, $user->id);
            return redirect()->route('transfers.index')->with('success', "Transfer No. {$transfer->transfer_number} telah dikirim dan tercatat dalam transit.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receiveForm(int $id)
    {
        $transfer = StockTransfer::with(['fromBranch', 'toBranch', 'items'])->findOrFail($id);

        return view('transfers.receive', [
            'transfer' => $transfer,
        ]);
    }

    public function receivePost(Request $request, int $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        try {
            $transfer = $this->transferService->receiveTransfer($id, $request->items, $user->id);
            return redirect()->route('transfers.index')->with('success', "Transfer No. {$transfer->transfer_number} telah berhasil diterima dan stok cabang bertambah!");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
