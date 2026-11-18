<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi administrator.']);
            }

            // Set initial active branch
            $defaultBranch = $user->getDefaultBranch();
            if ($defaultBranch) {
                session(['active_branch_id' => $defaultBranch->id]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = Auth::user();
        $branchId = (int) $request->branch_id;

        if (! $user->canAccessBranch($branchId)) {
            return back()->with('error', 'Anda tidak memiliki hak akses ke cabang ini.');
        }

        session(['active_branch_id' => $branchId]);
        $branch = Branch::find($branchId);

        return back()->with('success', "Cabang aktif dialihkan ke: {$branch->name}");
    }
}
