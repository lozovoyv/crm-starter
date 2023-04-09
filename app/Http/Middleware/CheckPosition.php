<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Current;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\PositionType;
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
            return ApiResponse::forbidden();
        }

        $current = Current::init($request);

        $currentType = $current->position()->type_id ?? null;
        $currentType = $currentType !== null ? PositionType::typeToString($currentType) : null;

        if (in_array($currentType, $positions, true)) {
            return $next($request);
        }

        return APIResponse::forbidden();
    }
}
