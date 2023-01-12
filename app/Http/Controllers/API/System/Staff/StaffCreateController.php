<?php

namespace App\Http\Controllers\API\System\Staff;

use App\Current;
use App\Foundation\Casting;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\History\HistoryAction;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffCreateController extends ApiController
{
    protected array $titles = [
        'user_id' => 'Учётная запись',
        'lastname' => 'Фамилия',
        'firstname' => 'Имя',
        'patronymic' => 'Отчество',
        'display_name' => 'Отображаемое имя',
        'email' => 'Адрес электронной почты',
        'phone' => 'Телефон',
        'username' => 'Логин',
        'password' => 'Пароль',
        'status_id' => 'Статус',
        'roles' => 'Роли',
    ];

    protected array $rules = [
        'user_id' => 'nullable',
        'lastname' => 'required_without:user_id',
        'firstname' => 'required_without:user_id',
        'patronymic' => 'required_without:user_id',
        'display_name' => 'nullable',
        'email' => 'required_without_all:user_id,username',
        'phone' => 'nullable',
        'username' => 'required_without_all:user_id,email',
        'password' => 'required_without:user_id',
        'status_id' => 'required',
        'roles' => 'nullable',
    ];

    protected array $messages = [
        'email.unique' => 'Учётная запись с таким email уже зарегистрирована',
        'phone.unique' => 'Учётная запись с таким телефоном уже зарегистрирована',
        'username.unique' => 'Учётная запись с таким именем пользователя уже зарегистрирована',
    ];

    /**
     * Get staff position data.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $position = new Position();
        $position->type_id = PositionType::staff;

        return APIResponse::form(
            'Добавление сотрудника',
            [
                'user_id' => null,
                'lastname' => null,
                'firstname' => null,
                'patronymic' => null,
                'display_name' => null,
                'email' => null,
                'phone' => null,
                'username' => null,
                'password' => null,
                'status_id' => $position->status_id,
                'roles' => null,
            ],
            null,
            $this->rules,
            $this->titles,
            []
        );
    }

    /**
     * Update or create staff position.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $position = new Position();
        $position->type_id = PositionType::staff;

        $data = $this->data($request);

        if ($data['user_id']) {
            // check this user already has staff position
            $exists = Position::query()->whereHas('user', function (Builder $query) use ($data) {
                    $query->where('id', $data['user_id']);
                })->count() > 0;
            if ($exists) {
                return APIResponse::validationError(['user_id' => ['Выбранный пользователь уже зарегистрирован как сотрудник']],
                    'Выбранный пользователь уже зарегистрирован как сотрудник');
            }
        } else {
            // upgrade rules to check user can be created
            $this->rules['email'] = 'required_without_all:user_id,username|unique:users';
            $this->rules['phone'] = 'nullable|unique:users';
            $this->rules['username'] = 'required_without_all:user_id,email|unique:users';
            $this->rules['password'] = 'required|min:6';
        }

        if ($errors = $this->validate($data, $this->rules, $this->titles, $this->messages)) {
            return APIResponse::validationError($errors);
        }

        /** @var User|null $user */
        $user = $data['user_id'] ? User::query()->where('id', $data['user_id'])->first() : new User();

        if ($user === null) {
            return APIResponse::validationError(['user_id' => ['Выбранный пользователь не найден']],
                'Пользователь не найден');
        }

        $current = Current::get($request);

        DB::transaction(function () use ($position, $user, $data, $current) {
            $userChanges = [];
            $this->set($user, 'lastname', $data['lastname'], Casting::string, $userChanges);
            $this->set($user, 'firstname', $data['firstname'], Casting::string, $userChanges);
            $this->set($user, 'patronymic', $data['patronymic'], Casting::string, $userChanges);
            $this->set($user, 'display_name', $data['display_name'], Casting::string, $userChanges);
            $this->set($user, 'email', $data['email'], Casting::string, $userChanges);
            $this->set($user, 'phone', $data['phone'], Casting::string, $userChanges);
            $this->set($user, 'username', $data['username'], Casting::string, $userChanges);
            $user->password = Hash::make($data['password']);
            $user->save();
            $user->addHistory(HistoryAction::user_created, $current->positionId())->addChanges($userChanges);

            $positionChanges = [];
            $this->set($position, 'user_id', $user->id, Casting::int, $positionChanges);
            $this->set($position, 'status_id', $data['status_id'], Casting::int, $positionChanges);
            $position->history_line_id = $user->history_line_id;
            $position->save();
            $position->roles()->sync($data['roles']);
            $positionChanges[] = ['parameter' => 'roles', 'type' => Casting::array, 'old' => null, 'new' => $data['roles']];
            $position->addHistory(HistoryAction::staff_position_created, $current->positionId())->addChanges($positionChanges);
        });

        return APIResponse::success('OK');
    }
}
