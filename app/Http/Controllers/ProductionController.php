<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Recipe;
use App\Services\ProductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function __construct(
        protected ProductionService $productionService
    ) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $query = ProductionOrder::with(['recipe.product.unit', 'branch', 'pic'])
            ->where('company_id', $user->company_id)
            ->latest('created_at');

        if ($branchId && ! $user->hasRole(['owner', 'admin_pusat'])) {
            $query->where('branch_id', $branchId);
        }

        $orders = $query->paginate(15);

        return view('production.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $recipes = Recipe::with(['product.unit', 'items.ingredient.unit'])
            ->where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        return view('production.create', [
            'recipes' => $recipes,
        ]);
    }

    public function calculatePreview(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'target_quantity' => 'required|numeric|min:0.01',
        ]);

        $branchId = session('active_branch_id');
        $calc = $this->productionService->calculateRequirements($branchId, (int) $request->recipe_id, (float) $request->target_quantity);

        return response()->json($calc);
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'target_quantity' => 'required|numeric|min:0.01',
            'planned_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('active_branch_id');
        $user = Auth::user();

        $order = $this->productionService->createOrder(
            $branchId,
            (int) $request->recipe_id,
            (float) $request->target_quantity,
            $request->planned_date,
            $request->notes,
            $user->id
        );

        return redirect()->route('production.index')->with('success', "Order produksi No. {$order->order_number} berhasil dibuat.");
    }

    public function finishForm(int $id)
    {
        $order = ProductionOrder::with(['recipe.product.unit', 'materials.ingredient.unit', 'branch'])->findOrFail($id);

        return view('production.finish', [
            'order' => $order,
        ]);
    }

    public function finishPost(Request $request, int $id)
    {
        $request->validate([
            'actual_good_quantity' => 'required|numeric|min:0.01',
            'actual_waste_quantity' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'overhead_cost' => 'nullable|numeric|min:0',
            'materials' => 'required|array',
            'materials.*.ingredient_id' => 'required|exists:ingredients,id',
            'materials.*.actual_quantity' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $user = Auth::user();

        try {
            $order = $this->productionService->finishAndPostOrder(
                $id,
                $request->materials,
                (float) $request->actual_good_quantity,
                (float) ($request->actual_waste_quantity ?? 0),
                (float) ($request->labor_cost ?? 0),
                (float) ($request->overhead_cost ?? 0),
                null,
                $request->expiry_date,
                $user->id
            );

            return redirect()->route('production.index')->with('success', "Order #{$order->order_number} selesai diposting. HPP: Rp " . number_format($order->unit_cost, 0, ',', '.') . "/unit. Stok produk jadi telah bertambah!");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
