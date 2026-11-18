<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    public function ingredients()
    {
        $user = Auth::user();
        $ingredients = Ingredient::with(['unit', 'category'])
            ->where('company_id', $user->company_id)
            ->paginate(15);
        $categories = Category::where('company_id', $user->company_id)->where('type', 'ingredient')->get();
        $units = Unit::where('company_id', $user->company_id)->get();

        return view('master.ingredients', [
            'ingredients' => $ingredients,
            'categories' => $categories,
            'units' => $units,
        ]);
    }

    public function products()
    {
        $user = Auth::user();
        $products = Product::with(['unit', 'category'])
            ->where('company_id', $user->company_id)
            ->paginate(15);
        $categories = Category::where('company_id', $user->company_id)->where('type', 'product')->get();
        $units = Unit::where('company_id', $user->company_id)->get();

        return view('master.products', [
            'products' => $products,
            'categories' => $categories,
            'units' => $units,
        ]);
    }
}
