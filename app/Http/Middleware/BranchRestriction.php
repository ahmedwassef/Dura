<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Super admins can access everything
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // If a branch_id is provided in the request, ensure it matches the user's branch
        if ($request->has('branch_id') && $request->input('branch_id') != $user->branch_id) {
            abort(403, 'Unauthorized branch access.');
        }

        // For route parameters like {user}, {branch}, etc.
        $params = $request->route()->parameters();
        
        foreach ($params as $param) {
            if ($param instanceof \App\Models\User && $param->branch_id != $user->branch_id) {
                abort(403, 'Unauthorized branch access to this user.');
            }
            if ($param instanceof \App\Models\Branch && $param->id != $user->branch_id) {
                abort(403, 'Unauthorized branch access.');
            }
        }

        return $next($request);
    }
}
