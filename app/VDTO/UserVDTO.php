<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\VDTO;

use App\Models\Users\User;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @property string $lastname
 * @property string $firstname
 * @property string $patronymic
 * @property string $display_name
 * @property string $phone
 * @property int $status_id
 * @property string $email
 * @property string $email_confirmation_need
 * @property string $username
 * @property bool $clear_password
 * @property string $new_password
 */
class UserVDTO extends VDTO implements VDTOInterface
{
    protected array $rules = [
        'lastname' => 'required_without:display_name',
        'firstname' => 'required',
        'patronymic' => 'nullable',
        'display_name' => 'required_without:lastname',
        'phone' => 'nullable|string|size:11',
        'status_id' => 'required|exists:user_statuses,id',
        'email' => 'required_without:username|email|nullable',
        'email_confirmation_need' => 'nullable',
        'username' => 'required_without:lastname,email',
        'clear_password' => 'nullable',
        'new_password' => 'nullable|min:6',
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
     * Validate data and return validation errors.
     *
     * @param array $only
     * @param User|null $user
     *
     * @return  array|null
     */
    public function validate(array $only = [], User $user = null): ?array
    {
        $rules = $this->rules;
        $rules['email'] = [...explode('|', $rules['email']), Rule::unique('users', 'email')->ignore($user)];
        $rules['phone'] = [...explode('|', $rules['phone']), Rule::unique('users', 'phone')->ignore($user)];
        $rules['username'] = [...explode('|', $rules['username']), Rule::unique('users', 'username')->ignore($user)];

        if (!empty($only)) {
            $rules = Arr::only($rules, $only);
        }

        return $this->validateAttributes($this->attributes, $rules, $this->titles, $this->messages);
    }
}
