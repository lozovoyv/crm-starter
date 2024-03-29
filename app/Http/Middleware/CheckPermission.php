<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Current;
use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Check access ability.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$permissions
     *
     * @return  mixed
     *
     */
    public function handle(Request $request, Closure $next, ...$permissions): mixed
    {
        if (empty($permissions)) {
            return ApiResponse::forbidden();
        }

        $current = Current::init($request);

        foreach ($permissions as $permission) {
            if ($current->can($permission)) {
                return $next($request);
            }
        }

        return APIResponse::forbidden();
    }
}
