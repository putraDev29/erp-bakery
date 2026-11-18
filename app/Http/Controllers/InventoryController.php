<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $selectedType = $request->input('type', 'all');

        $query = StockBalance::with(['ingredient.unit', 'product.unit', 'branch'])
            ->where('branch_id', $branchId);

        if ($selectedType === 'ingredient') {
            $query->where('item_type', 'ingredient');
        } elseif ($selectedType === 'product') {
            $query->where('item_type', 'product');
        }

        $balances = $query->paginate(20);

        return view('inventory.index', [
            'balances' => $balances,
            'selectedType' => $selectedType,
        ]);
    }

    public function ledger(Request $request)
    {
        $branchId = session('active_branch_id');
        $user = Auth::user();

        $itemType = $request->input('item_type', 'ingredient');
        $itemId = $request->input('item_id');

        $movements = StockMovement::with(['ingredient.unit', 'product.unit', 'createdBy'])
            ->where('branch_id', $branchId)
            ->latest('created_at');

        if ($itemType && $itemId) {
            $movements->where('item_type', $itemType)->where('item_id', $itemId);
        }

        $history = $movements->paginate(25);

        $ingredients = Ingredient::where('company_id', $user->company_id)->get();
        $products = Product::where('company_id', $user->company_id)->get();

        return view('inventory.ledger', [
            'history' => $history,
            'ingredients' => $ingredients,
            'products' => $products,
            'selectedItemType' => $itemType,
            'selectedItemId' => $itemId,
        ]);
    }
}
