<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserCRUDController extends ApiController
{
    protected array $rules = [
        'lastname' => 'required_without:display_name',
        'firstname' => 'required',
        'patronymic' => 'nullable',
        'display_name' => 'required_without:lastname',
        'username' => 'required_without:lastname',
        'phone' => 'nullable|string|required_without:lastname|size:11',
        'status_id' => 'required_without:lastname',
        'new_password' => 'nullable|min:6',
        'clear_password' => 'nullable',
        'email' => 'required_without:lastname|email',
        'email_confirmation_need' => 'nullable',
    ];

    protected array $titles = [
        'lastname' => 'Фамилия',
        'firstname' => 'Имя',
        'patronymic' => 'Отчество',
        'display_name' => 'Отображаемое имя',
        'username' => 'Логин',
        'phone' => 'Телефон',
        'status_id' => 'Статус учётной записи',
        'new_password' => 'Новый пароль',
        'clear_password' => 'Удалить пароль',
        'email' => 'Адрес электронной почты',
        'email_confirmation_need' => 'Запросить подтверждение адреса электронной почты',
    ];

    protected array $messages = [
        'username.unique' => 'Данный логин уже занят',
        'email.unique' => 'Данный адрес электронной почты уже используется',
        'phone.unique' => 'Данный телефон уже используется',
        'phone.size' => 'Номер телефона некорректно заполнен',
    ];

    /**
     * Get user data.
     *
     * @param Request $request
     * @param int|null $userId
     *
     * @return ApiResponse
     */
    public function get(Request $request, ?int $userId = null): ApiResponse
    {
        /** @var User $user */
        try {
            $user = User::get($userId, null, false, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::form()
            ->title($user->exists ? $user->fullName : 'Создание учётной записи')
            ->values([
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'patronymic' => $user->patronymic,
                'display_name' => $user->display_name,
                'status_id' => $user->status_id,
                'username' => $user->username,
                'email' => $user->email,
                'email_confirmation_need' => true,
                'new_password' => null,
                'clear_password' => false,
                'phone' => $user->phone,
            ])
            ->rules($this->rules)
            ->titles($this->titles)
            ->messages($this->messages)
            ->hash($user->getHash())
            ->payload([
                'has_password' => !empty($user->password),
            ]);
    }

    /**
     * Update user data.
     *
     * @param Request $request
     * @param int|null $userId
     *
     * @return  ApiResponse
     * @noinspection DuplicatedCode
     */
    public function save(Request $request, ?int $userId = null): ApiResponse
    {
        try {
            $user = User::get($userId, $request->input('hash'), true, false);
            $data = $this->data($request);
            $current = Current::init($request);

            $this->rules['email'] = ['bail', 'email', Rule::unique('users', 'email')->ignore($user)];
            $this->rules['phone'] = [...explode('|', $this->rules['phone']), Rule::unique('users', 'phone')->ignore($user)];
            $this->rules['username'] = [Rule::unique('users', 'username')->ignore($user)];

            if ($errors = $this->validate($data, $this->rules, $this->titles, $this->messages)) {
                return APIResponse::validationError($errors);
            }

            $user
                ->change($data['lastname'], $data['firstname'], $data['patronymic'], $data['display_name'], $data['username'], $data['phone'], $current)
                ->changeStatus($data['status_id'], $current)
                ->changePassword($data['new_password'], $data['clear_password'] ?? false, $current)
                ->changeEmail($data['email'], $data['email_confirmation_need'] ?? false, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success($user->wasRecentlyCreated ? 'Учётная запись добавлена' : 'Учётная запись сохранена')
            ->payload(['id' => $user->id]);
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function password(Request $request, int $userID): ApiResponse
    {
        try {
            $user = User::get($userID, $request->input('hash'), true, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);
        $current = Current::init($request);

        if ($errors = $this->validate($data, ['new_password' => 'nullable|min:6', 'clear_password' => 'nullable'], ['password' => 'Пароль'])) {
            return APIResponse::validationError($errors);
        }

        $user->changePassword($data['new_password'] ?? null, $data['clear_password'] ?? false, $current);

        return APIResponse::success('Пароль сохранён');
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function email(Request $request, int $userID): ApiResponse
    {
        try {
            $user = User::get($userID, $request->input('hash'), true, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);
        $current = Current::init($request);

        $rules['email'] = ['bail', 'nullable', 'email', Rule::unique('users', 'email')->ignore($user)];

        if ($errors = $this->validate($data, $rules, ['email' => 'Адрес электронной почты'])) {
            return APIResponse::validationError($errors);
        }

        $needConfirmation = $data['email_confirmation_need'] ?? false;

        $user->changeEmail($data['email'] ?? null, $needConfirmation, $current);

        return APIResponse::success(
            $needConfirmation && !empty($data['email']) ? 'Запрос на подтверждение адреса электронной почты отправлен' : 'Адрес электронной почты сохранён'
        );
    }

    /**
     * Change user status.
     *
     * @param int $userID
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function status(Request $request, int $userID): ApiResponse
    {
        try {
            $user = User::get($userID, $request->input('hash'), true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::init($request);

        $user->changeStatus($request->boolean('disable') ? UserStatus::blocked : UserStatus::active, $current);

        return APIResponse::success($user->hasStatus(UserStatus::active) ? 'Учётная запись активирована' : 'Учётная запись заблокирована');
    }

    /**
     * Delete user.
     *
     * @param Request $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function remove(Request $request, int $userID): ApiResponse
    {
        try {
            $user = User::get($userID, $request->input('hash'), true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::init($request);

        if ($current->userId() === $user->id) {
            return APIResponse::error('Вы не можете удалить собственную учётную запись');
        }

        try {
            $user->remove($current);
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить учётную запись.');
        }

        return ApiResponse::success('Учётная запись удалена');
    }
}
