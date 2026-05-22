<?php

namespace App\Http\Middleware;

use App\Services\Clinic\ClinicSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicRole
{
    public function __construct(
        protected ClinicSessionService $clinicSession,
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $this->clinicSession->getRole();

        if ($role && in_array($role, $roles, true)) {
            return $next($request);
        }

        $user = $request->user();

        if ($user) {
            foreach ($roles as $allowed) {
                if ($user->hasRole($allowed)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Clinic role required.');
    }
}
