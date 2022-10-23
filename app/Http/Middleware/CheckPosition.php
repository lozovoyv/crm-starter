<?php

namespace App\Http\Middleware;

use App\Current;
use App\Http\APIResponse;
use Closure;
use Illuminate\Http\Request;

class CheckPosition
{
    /**
     * Check access ability.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$positions
     *
     * @return  mixed
     *
     */
    public function handle(Request $request, Closure $next, ...$positions): mixed
    {
        if (empty($positions)) {
            return APIResponse::forbidden();
        }

        $current = Current::get($request);

//        foreach ($permissions as $permission) {
//            if ($current->can($permission)) {
                return $next($request);
//            }
//        }

//        return APIResponse::forbidden();
    }
}
