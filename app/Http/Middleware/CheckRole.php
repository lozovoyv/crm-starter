<?php

namespace App\Http\Middleware;

use App\Current;
use App\Http\APIResponse;
use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Check role ability.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     *
     * @return  mixed
     *
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (empty($roles)) {
            return APIResponse::forbidden();
        }

        $current = Current::get($request);

        foreach ($roles as $role) {
            if ($current->hasRole($role)) {
                return $next($request);
            }
        }

        return APIResponse::forbidden();
    }
}
