<?php

namespace App\Http\Controllers\Auth;

use App\Current;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\Dictionaries\UserStatus;
use App\Models\User\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    /**
     * Get current user info.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function current(Request $request): JsonResponse
    {
        $current = Current::get($request);

        $user = !$current->isAuthenticated() ? null : [
            'id' => $current->userId(),
            'name' => $current->userName(),
            'organization' => 'OPX core',
        ];

        return APIResponse::response([
            'user' => $user,
            'permissions' => $current->permissions(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $data = $this->data($request);

        // validate request
        if ($errors = $this->validate($data, [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
        ])) {
            return APIResponse::validationError($errors);
        }

        // authenticate
        if ($errors = $this->authenticate($request)) {
            return APIResponse::validationError($errors, 'Ошибка');
        }

        /** @var User $user */
        $user = $request->user();

        if ($user->tokens()->count() === 0) {
            $user->createToken('base_token');
        }

        $request->session()->regenerate();

        return APIResponse::success('OK');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return APIResponse::success('OK');
    }

    /**
     * Attempt to authenticate the request credentials.
     *
     * @param Request $request
     *
     * @return array|null
     */
    protected function authenticate(Request $request): ?array
    {
        if ($errors = $this->validateRateLimited($request)) {
            return $errors;
        }

        if (!Auth::attempt($this->credentials($request), $this->remember($request))) {

            RateLimiter::hit($this->throttleKey($request));

            return ['username' => [__('auth.failed')]];
        }

        RateLimiter::clear($this->throttleKey($request));

        return null;
    }

    /**
     * Get credentials to authenticate.
     *
     * @param Request $request
     *
     * @return  array
     */
    protected function credentials(Request $request): array
    {
        return [
            'username' => $request->input('data.username'),
            'password' => $request->input('data.password'),
            'status_id' => UserStatus::active,
        ];
    }

    /**
     * Is remember option enabled?
     *
     * @param Request $request
     *
     * @return  bool
     */
    protected function remember(Request $request): bool
    {
        return $request->boolean('remember');
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param Request $request
     *
     * @return  null|array
     */
    protected function validateRateLimited(Request $request): ?array
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return null;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        return ['username' => [trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60),])]];
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param Request $request
     *
     * @return  string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('username')) . '|' . $request->ip();
    }
}
