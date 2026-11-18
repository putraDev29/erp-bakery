<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branches = Branch::where('company_id', $user->company_id)->withCount('users')->get();

        return view('branches.index', [
            'branches' => $branches,
        ]);
    }
}
