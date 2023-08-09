<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APILoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
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
     * @param APILoginRequest $request
     *
     * @return  ApiResponse
     */
    public function login(APILoginRequest $request): ApiResponse
    {
        // authenticate
        if ($errors = $this->authenticate($request)) {
            return APIResponse::validationError($errors, 'Ошибка');
        }

        /** @var User $user */
        $user = $request->user();

        // todo check and refactor this
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
            $positions = $user->positions()->whereIn('type_id', [PositionType::admin, PositionType::staff])->get();

            /** @var Position $position */
            $position = $positions->first();
            $session->put('position_id', $position->id);
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
     * @param APILoginRequest $request
     *
     * @return array|null
     */
    protected function authenticate(APILoginRequest $request): ?array
    {
        if ($errors = $this->validateRateLimited($request)) {
            return $errors;
        }

        if (!Auth::attempt($request->credentials(), $request->remember())) {

            RateLimiter::hit($request->throttleKey());

            return ['username' => [__('auth.failed')]];
        }

        RateLimiter::clear($request->throttleKey());

        return null;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param APILoginRequest $request
     *
     * @return  null|array
     */
    protected function validateRateLimited(APILoginRequest $request): ?array
    {
        if (!RateLimiter::tooManyAttempts($request->throttleKey(), 5)) {
            return null;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($request->throttleKey());

        return ['username' => [trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60),])]];
    }
}
