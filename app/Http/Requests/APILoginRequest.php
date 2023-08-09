<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Users\UserStatus;
use Illuminate\Support\Str;

class APILoginRequest extends APIBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData(): array
    {
        return $this->only(['username', 'password']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'username' => mb_strtolower(trans('auth.login_username')),
            'password' => mb_strtolower(trans('auth.login_password')),
        ];
    }

    /**
     * Get credentials to authenticate.
     *
     * @return  array
     */
    public function credentials(): array
    {
        $credentials = [
            'password' => $this->input('password'),
            'status_id' => UserStatus::active,
        ];

        $login = $this->input('username');

        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials[$type] = $login;

        return $credentials;
    }

    /**
     * Is remember option enabled
     *
     * @return  bool
     */
    public function remember(): bool
    {
        return $this->boolean('remember');
    }


    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return  string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('username')) . '|' . $this->ip();
    }
}
