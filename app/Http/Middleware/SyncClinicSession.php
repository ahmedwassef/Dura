<?php

namespace App\Http\Middleware;

use App\Services\Clinic\ClinicSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncClinicSession
{
    public function __construct(
        protected ClinicSessionService $clinicSession,
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->clinicSession->syncFromRequest($request, $request->user());

        return $next($request);
    }
}
