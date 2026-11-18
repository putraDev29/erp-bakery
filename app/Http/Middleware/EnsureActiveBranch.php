<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        $activeBranchId = session('active_branch_id');

        if (! $activeBranchId) {
            $defaultBranch = $user->getDefaultBranch();
            if ($defaultBranch) {
                session(['active_branch_id' => $defaultBranch->id]);
                $activeBranchId = $defaultBranch->id;
            }
        }

        // Validate that user is genuinely allowed to access this branch
        if ($activeBranchId && ! $user->canAccessBranch((int) $activeBranchId)) {
            $defaultBranch = $user->getDefaultBranch();
            if ($defaultBranch) {
                session(['active_branch_id' => $defaultBranch->id]);
            } else {
                abort(403, 'Anda tidak memiliki hak akses ke cabang ini.');
            }
        }

        // Share current branch with views
        if (session('active_branch_id')) {
            $currentBranch = Branch::find(session('active_branch_id'));
            view()->share('currentBranch', $currentBranch);
        }

        return $next($request);
    }
}
