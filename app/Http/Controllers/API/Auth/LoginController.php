<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends ApiController
{
    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function login(Request $request): ApiResponse
    {
        $data = $request->input('data');

        // validate request
        //if ($errors = $this->validate($data, [
        //    'username' => 'required|string',
        //    'password' => 'required|string',
        //], [
        //    'username' => 'Имя пользователя',
        //    'password' => 'Пароль',
        //])) {
        //    return ApiResponse::validationError($errors);
        //}

        // authenticate
        if ($errors = $this->authenticate($request)) {
            return APIResponse::validationError($errors, 'Ошибка');
        }

        /** @var User $user */
        $user = $request->user();

        if ($user->tokens()->count() === 0) {
            $user->createToken('base_token');
        }

        $session = $request->session();

        $session->regenerate();

        // Check user can have position stored in session
        if ($session->has('position_id')) {
            /** @var Position|null $position */
            $position = $user->positions()->where('id', $session->get('position_id'))->where('type_id', PositionType::staff)->first();
            if ($position === null) {
                $session->remove('position_id');
            }
        }

        if (!$session->has('position_id')) {
            $positions = $user->positions()->where('type_id', PositionType::staff)->get();
            if ($positions->count() === 1) {
                /** @var Position $position */
                $position = $positions->first();
                $session->put('position_id', $position->id);
            }
            // else {
            // TODO make position select response
            //}
        }

        return APIResponse::success('OK');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function logout(Request $request): ApiResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Current::unset();

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
        $credentials = [
            'password' => $request->input('data.password'),
            'status_id' => UserStatus::active,
        ];

        $login = $request->input('data.username');

        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials[$type] = $login;

        return $credentials;
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
        return $request->boolean('data.remember');
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
