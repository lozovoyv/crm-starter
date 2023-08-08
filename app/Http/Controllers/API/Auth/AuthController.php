<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * Get current user info.
     *
     * @param Request $request
     *
     * @return  APIResponse
     */
    public function current(Request $request): APIResponse
    {
        $current = Current::init($request);

        $user = !$current->isAuthenticated() ? null : [
            'id' => $current->userId(),
            'name' => $current->compactName(),
            'email' => $current->email(),
            'organization' => null,
            'position' => $current->position()->type->name,
            'avatar' => null,
        ];

        return ApiResponse::common([
            'user' => $user,
            'permissions' => $current->permissions(),
        ]);
    }

    /**
     * Get login options.
     *
     * @return  APIResponse
     */
    public function config(): APIResponse
    {
        return ApiResponse::common([
            'registration_enabled' => config('auth.registration_enabled'),
            'reset_password_enabled' => config('auth.reset_password_enabled'),
        ]);
    }
}
