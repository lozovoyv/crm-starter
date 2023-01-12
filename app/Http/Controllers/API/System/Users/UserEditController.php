<?php

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Foundation\Casting;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserEditController extends ApiController
{
    protected array $rules = [
        'lastname' => 'required_without:display_name',
        'firstname' => 'required',
        'patronymic' => 'nullable',
        'display_name' => 'required_without:lastname',
        'status_id' => 'required_without:lastname',
        'username' => 'required_without:lastname',
        'email' => 'required_without:lastname',
        'email_confirmation_need' => 'nullable',
        'new_password' => 'nullable|min:6',
        'clear_password' => 'nullable',
        'phone' => 'required_without:lastname',
    ];

    protected array $titles = [
        'lastname' => 'Фамилия',
        'firstname' => 'Имя',
        'patronymic' => 'Отчество',
        'display_name' => 'Отображаемое имя',
        'status_id' => 'Статус учётной записи',
        'username' => 'Логин',
        'email' => 'Адрес электронной почты',
        'email_confirmation_need' => 'Запросить подтверждение адреса электронной почты',
        'new_password' => 'Новый пароль',
        'clear_password' => 'Удалить пароль',
        'phone' => 'Телефон',
    ];

    /**
     * Get user data.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->firstOrNew(User::class, $request->input('user_id'));

        if ($user === null) {
            return APIResponse::notFound('Учётная запись не найдена');
        }

        return APIResponse::form(
            $user->exists ? $user->fullName : 'Создание учётной записи',
            [
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
            ],
            $user->getHash(),
            $this->rules,
            $this->titles,
            [
                'has_password' => !empty($user->password),
            ]
        );
    }

    /**
     * Update user data.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->firstOrNew(User::class, $request->input('user_id'));

        if ($user === null) {
            return APIResponse::notFound('Учётная запись не найдена');
        }

        $data = $this->data($request);

        $this->rules['email'] = [
            'bail',
            'email',
            Rule::unique('users', 'email')->ignore($user),
        ];
        $this->rules['phone'] = [
            Rule::unique('users', 'phone')->ignore($user),
        ];
        $this->rules['username'] = [
            Rule::unique('users', 'username')->ignore($user),
        ];

        if ($errors = $this->validate($data, $this->rules, $this->titles, [
            'email.unique' => 'Данный адрес электронной почты уже используется',
            'phone.unique' => 'Данный телефон уже используется',
            'username.unique' => 'Данный логин уже занят',
        ])) {
            return APIResponse::validationError($errors);
        }

        $current = Current::get($request);

        $changes = [];
        $this->set($user, 'lastname', $data['lastname'], Casting::string, $changes);
        $this->set($user, 'firstname', $data['firstname'], Casting::string, $changes);
        $this->set($user, 'patronymic', $data['patronymic'], Casting::string, $changes);
        $this->set($user, 'display_name', $data['display_name'], Casting::string, $changes);
        $this->set($user, 'email', $data['email'], Casting::string, $changes);
        $this->set($user, 'username', $data['username'], Casting::string, $changes);
        $this->set($user, 'phone', $data['phone'], Casting::string, $changes);
        $oldUserStatus = $user->status_id;
        $user->setStatus($data['status_id']);
        $user->save();

        if ($oldUserStatus !== $user->status_id) {
            if ($user->hasStatus(UserStatus::active)) {
                $user->addHistory(HistoryAction::user_activated, $current->positionId());
            } else {
                $user->addHistory(HistoryAction::user_deactivated, $current->positionId());
            }
        }

        if ($data['clear_password']) {
            if (!empty($user->password)) {
                $user->password = null;
                $user->save();
                $user->addHistory(HistoryAction::user_password_cleared, $current->positionId());
            }
        } else if (!empty($data['new_password'])) {
            $hadPassword = !empty($user->password);
            $user->password = Hash::make($data['new_password']);
            $user->save();
            if ($hadPassword) {
                $user->addHistory(HistoryAction::user_password_changed, $current->positionId());
            } else {
                $user->addHistory(HistoryAction::user_password_set, $current->positionId());
            }
        }

        if (!empty($changes)) {
            $user
                ->addHistory($user->wasRecentlyCreated ? HistoryAction::user_created : HistoryAction::user_edited, $current->positionId())
                ->addChanges($changes);
        } else {
            return APIResponse::success(
                'Изменений не сделано',
                ['id' => $user->id]
            );
        }

        return APIResponse::success(
            $user->wasRecentlyCreated ? 'Учётная запись добавлена' : 'Учётная запись сохранена',
            ['id' => $user->id]
        );
    }
}
