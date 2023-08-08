<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class RegisterController extends ApiController
{
    /**
     * Register new user.
     *
     * @param Request $request
     *
     * @return  APIResponse
     */
    public function register(Request $request): APIResponse
    {
        if(!env('AUTH_REGISTRATION_ENABLED', false)) {
            return ApiResponse::forbidden('registration is disabled');
        }

        return ApiResponse::success();
    }
}
