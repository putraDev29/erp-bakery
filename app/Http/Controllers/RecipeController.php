<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recipes = Recipe::with(['product.unit', 'yieldUnit', 'items.ingredient.unit'])
            ->where('company_id', $user->company_id)
            ->latest()
            ->paginate(15);

        return view('recipes.index', [
            'recipes' => $recipes,
        ]);
    }

    public function show(int $id)
    {
        $recipe = Recipe::with(['product.unit', 'yieldUnit', 'items.ingredient.unit'])->findOrFail($id);

        return view('recipes.show', [
            'recipe' => $recipe,
        ]);
    }
}
