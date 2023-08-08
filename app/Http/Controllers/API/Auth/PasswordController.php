<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class PasswordController extends ApiController
{
    /**
     * Send password reset link.
     *
     * @param Request $request
     *
     * @return  APIResponse
     */
    public function forget(Request $request): APIResponse
    {
        if (!env('AUTH_RESET_PASSWORD_ENABLED', false)) {
            return ApiResponse::forbidden('password reset is disabled');
        }

        return ApiResponse::success();
    }

    /**
     * Reset password.
     *
     * @param Request $request
     *
     * @return  APIResponse
     */
    public function reset(Request $request): APIResponse
    {
        if (!env('AUTH_RESET_PASSWORD_ENABLED', false)) {
            return ApiResponse::forbidden('password reset is disabled');
        }

        return ApiResponse::success();
    }
}
