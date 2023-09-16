<?php
declare(strict_types=1);

namespace App\Resources\Users;

use App\Current;
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\Model\ModelWrongHashException;
use App\Mail\EmailChange;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChanges;
use App\Models\Users\User;
use App\Models\Users\UserEmailConfirmation;
use App\Models\Users\UserStatus;
use App\Resources\EntryResource;
use App\Utils\Casting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class UserEntryResource extends EntryResource
{
    protected array $rules = [
        'lastname' => 'required_without:display_name',
        'firstname' => 'required',
        'patronymic' => 'nullable',
        'display_name' => 'required_without:lastname',
        'username' => 'required_without:lastname',
        'phone' => 'nullable|string|size:11',
        'status_id' => 'required',
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
     * Get user.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param bool $onlyExisting
     *
     * @return User
     *
     * @throws ModelLockedException
     * @throws ModelWrongHashException
     * @throws ModelNotFoundException
     */
    public function get(?int $id, ?string $hash = null, bool $check = false, bool $onlyExisting = true): User
    {
        /** @var User|null $user */
        if ($id === null) {
            $user = $onlyExisting ? null : new User();
        } else {
            $user = User::query()->where('id', $id)->first();
        }

        if ($user === null) {
            throw new ModelNotFoundException('Учётная запись не найдена');
        }
        if ($check && $user->exists && !$user->isHash($hash)) {
            throw new ModelWrongHashException('Учётная запись была изменена в другом месте.');
        }
        if ($check && $user->locked) {
            throw new ModelLockedException('Эту учётную запись нельзя изменить или удалить');
        }

        return $user;
    }

    /**
     * Get user hash.
     *
     * @param User $user
     *
     * @return string|null
     */
    public function getHash(User $user): ?string
    {
        return $user->getHash();
    }

    /**
     * Get data from request.
     *
     * @param array $data
     * @param array $only
     *
     * @return  array
     */
    public function filterData(array $data, array $only = []): array
    {
        if (empty($only)) {
            $only = [
                'lastname',
                'firstname',
                'patronymic',
                'display_name',
                'username',
                'phone',
                'status_id',
                'new_password',
                'clear_password',
                'email',
                'email_confirmation_need',
            ];
        }

        return parent::filterData($data, $only);
    }

    /**
     * Get formatted fields values for user
     *
     * @param User $user
     *
     * @return array
     */
    public function getValues(User $user): array
    {
        return [
            'id' => $user->id,
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
        ];
    }

    /**
     * Validate data and return validation errors.
     *
     * @param array $data
     * @param User $user
     * @param array $only
     *
     * @return  array|null
     */
    public function validate(array $data, User $user, array $only = []): ?array
    {
        $rules = $this->rules;
        $rules['email'] = ['bail', 'email', Rule::unique('users', 'email')->ignore($user)];
        $rules['phone'] = [...explode('|', $rules['phone']), Rule::unique('users', 'phone')->ignore($user)];
        $rules['username'] = [Rule::unique('users', 'username')->ignore($user)];

        if (!empty($only)) {
            $rules = Arr::only($rules, $only);
        }

        return $this->validateData($data, $rules, $this->titles, $this->messages);
    }

    /**
     * Update user data.
     *
     * @param User $user
     * @param array $data
     * @param Current $current
     *
     * @return User
     */
    public function update(User $user, array $data, Current $current): User
    {
        $changes = [];

        if (array_key_exists('lastname', $data)) {
            $changes[] = $user->setAttributeWithChanges('lastname', $data['lastname'], Casting::string);
        }
        if (array_key_exists('firstname', $data)) {
            $changes[] = $user->setAttributeWithChanges('firstname', $data['firstname'], Casting::string);
        }
        if (array_key_exists('patronymic', $data)) {
            $changes[] = $user->setAttributeWithChanges('patronymic', $data['patronymic'], Casting::string);
        }
        if (array_key_exists('display_name', $data)) {
            $changes[] = $user->setAttributeWithChanges('display_name', $data['display_name'], Casting::string);
        }
        if (array_key_exists('username', $data)) {
            $changes[] = $user->setAttributeWithChanges('username', $data['username'], Casting::string);
        }
        if (array_key_exists('phone', $data)) {
            $changes[] = $user->setAttributeWithChanges('phone', $data['phone'], Casting::string);
        }

        $changes = array_filter($changes);

        if (!empty($changes)) {
            $user->save();
            $user
                ->addHistory($user->wasRecentlyCreated ? HistoryAction::user_created : HistoryAction::user_edited, $current->positionId())
                ->addChanges($changes);
        }

        return $user;
    }

    /**
     * Change user status.
     *
     * @param User $user
     * @param array $data
     * @param Current $current
     *
     * @return User
     */
    public function updateStatus(User $user, array $data, Current $current): User
    {
        if (!array_key_exists('status_id', $data)) {
            return $user;
        }

        if ($data['status_id'] !== $user->status_id) {
            $user->setStatus($data['status_id']);
            $user->save();

            $action = match ($user->status_id) {
                UserStatus::active => HistoryAction::user_activated,
                UserStatus::blocked => HistoryAction::user_deactivated,
            };
            $user->addHistory($action, $current->positionId());
        }

        return $user;
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param array $data
     * @param Current $current
     *
     * @return User
     */
    public function updatePassword(User $user, array $data, Current $current): User
    {
        if (!array_key_exists('clear_password', $data) && !array_key_exists('new_password', $data)) {
            return $user;
        }

        if (!empty($data['clear_password'])) {
            if (!empty($user->password)) {
                $user->password = null;
                $user->save();
                $user->addHistory(HistoryAction::user_password_cleared, $current->positionId());
            }
        } else if (!empty($data['new_password'])) {
            $action = match (true) {
                empty($user->password) => HistoryAction::user_password_set,
                default => HistoryAction::user_password_changed,
            };
            $user->password = Hash::make($data['new_password']);
            $user->save();
            $user->addHistory($action, $current->positionId());
        }

        return $user;
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param array $data
     * @param Current $current
     *
     * @return User
     */
    public function updateEmail(User $user, array $data, Current $current): User
    {
        if (!array_key_exists('email', $data)) {
            return $user;
        }

        if ($user->email === $data['email']) {
            return $user;
        }

        if (empty($data['email_confirmation_need']) || $data['email'] === null) {

            $oldEmail = $user->email;
            $change = $user->setAttributeWithChanges('email', $data['email'], Casting::string);
            $user->save();

            $action = match (true) {
                ($oldEmail === null) => HistoryAction::user_email_set,
                ($data['email'] === null) => HistoryAction::user_email_cleared,
                default => HistoryAction::user_email_changed,
            };

            $user
                ->addHistory($action, $current->positionId())
                ->addChanges([$change]);

            return $user;
        }

        Mail::send(new EmailChange(UserEmailConfirmation::create($user, $data['email'])));

        $user->addHistory(HistoryAction::user_email_verification_sent, $current->positionId(), $data['email']);

        return $user;
    }

    /**
     * Apply new confirmed email.
     *
     * @param User $user
     * @param array $data
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function confirmNewEmail(User $user, array $data): void
    {
        DB::transaction(static function () use ($user, $data) {
            if (User::query()->where('email', $data['email'])->count()) {
                throw new InvalidArgumentException('Адрес электронной почты ' . $data['email'] . ' уже занят');
            }
            $change = $user->setAttributeWithChanges('email', $data['email'], Casting::string);
            $user->save();
            $user->addHistory(HistoryAction::user_email_verified, null)->addChanges([$change]);
        });
    }

    /**
     * Remove user.
     *
     * @param User $user
     * @param Current $current
     *
     * @return void
     * @throws ModelDeleteBlockedException
     */
    public function remove(User $user, Current $current): void
    {
        $changes = [
            new HistoryChanges(['parameter' => 'lastname', 'type' => Casting::string, 'old' => $user->lastname, 'new' => null]),
            new HistoryChanges(['parameter' => 'firstname', 'type' => Casting::string, 'old' => $user->firstname, 'new' => null]),
            new HistoryChanges(['parameter' => 'patronymic', 'type' => Casting::string, 'old' => $user->patronymic, 'new' => null]),
            new HistoryChanges(['parameter' => 'display_name', 'type' => Casting::string, 'old' => $user->display_name, 'new' => null]),
            new HistoryChanges(['parameter' => 'email', 'type' => Casting::string, 'old' => $user->email, 'new' => null]),
            new HistoryChanges(['parameter' => 'phone', 'type' => Casting::string, 'old' => $user->phone, 'new' => null]),
            new HistoryChanges(['parameter' => 'username', 'type' => Casting::string, 'old' => $user->username, 'new' => null]),
        ];

        $user
            ->addHistory(HistoryAction::user_deleted, $current->positionId())
            ->addChanges($changes);

        try {
            $user->delete();
        } catch (QueryException) {
            throw new ModelDeleteBlockedException('Невозможно удалить учётную запись. Она задействована в системе');
        }
    }

    /**
     * Cast user to array.
     *
     * @param User $user
     *
     * @return  array
     * @noinspection DuplicatedCode
     */
    public static function format(User $user): array
    {
        return [
            'id' => $user->id,
            'is_active' => $user->hasStatus(UserStatus::active),
            'locked' => $user->locked,
            'status' => $user->status->name,
            'lastname' => $user->lastname,
            'firstname' => $user->firstname,
            'patronymic' => $user->patronymic,
            'display_name' => $user->display_name,
            'username' => $user->username,
            'email' => $user->email,
            'has_password' => !empty($user->password),
            'phone' => $user->phone,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'hash' => $user->getHash(),
        ];
    }
}
