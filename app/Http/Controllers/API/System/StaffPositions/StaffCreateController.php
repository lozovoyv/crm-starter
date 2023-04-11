<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\StaffPositions;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\EntryScope;
use App\Models\History\HistoryAction;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffCreateController extends ApiController
{
    protected array $titles = [
        'mode' => 'Учётная запись',
        'user_id' => 'Учётная запись',
        'lastname' => 'Фамилия',
        'firstname' => 'Имя',
        'patronymic' => 'Отчество',
        'display_name' => 'Отображаемое имя',
        'email' => 'Адрес электронной почты',
        'phone' => 'Телефон',
        'username' => 'Логин',
        'password' => 'Пароль',
        'status_id' => 'Статус сотрудника',
        'roles' => 'Роли',
    ];

    protected array $rules = [
        'mode' => 'required',
        'user_id' => 'required_if:mode,existing',
        'lastname' => 'required_if:mode,new',
        'firstname' => 'required_if:mode,new',
        'patronymic' => 'required_if:mode,new',
        'display_name' => 'nullable',
        'email' => 'required_if:mode,new|email',
        'phone' => 'required_if:mode,new|size:11|bail',
        'username' => 'nullable',
        'password' => 'required_if:mode,new|min:6|bail',
        'status_id' => 'required',
        'roles' => 'nullable',
    ];

    protected array $messages = [
        'user_id.required_if' =>'Поле "Учётная запись" обязательно для заполнения',
        'email.unique' => 'Учётная запись с таким email уже зарегистрирована',
        'phone.unique' => 'Учётная запись с таким телефоном уже зарегистрирована',
        'phone.required_if' => 'Поле "Телефон" обязательно для заполнения',
        'username.unique' => 'Учётная запись с таким именем пользователя уже зарегистрирована',
        'lastname.required_if' => 'Поле "Фамилия" обязательно для заполнения',
        'firstname.required_if' => 'Поле "Имя" обязательно для заполнения',
        'patronymic.required_if' => 'Поле "Отчество" обязательно для заполнения',
        'email.required_if' => 'Поле "Адрес электронной почты" обязательно для заполнения, если не указан логин.',
        'username.required_without_all' => 'Поле "Логин" обязательно для заполнения, не указан адрес электронной почты.',
        'password.required_if' => 'Поле "Пароль" обязательно для заполнения.',
    ];

    /**
     * Get staff position data.
     *
     * @return  ApiResponse
     */
    public function get(): ApiResponse
    {
        $position = new Position();
        $position->type_id = PositionType::staff;

        return ApiResponse::form()
            ->title('Регистрация сотрудника')
            ->values([
                'mode' => null,
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
            ])
            ->rules($this->rules)
            ->titles($this->titles)
            ->messages($this->messages);
    }

    /**
     * Update or create staff position.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     * @noinspection DuplicatedCode
     */
    public function create(Request $request): ApiResponse
    {
        $position = new Position();
        $position->type_id = PositionType::staff;

        $data = $this->data($request);

        if ($data['user_id']) {
            // check this user already has staff position
            $exists = Position::query()
                    ->where('type_id', PositionType::staff)
                    ->whereHas('user', function (Builder $query) use ($data) {
                        $query->where('id', $data['user_id']);
                    })->count() > 0;
            if ($exists) {
                return APIResponse::validationError(['user_id' => ['Сотрудник с этой учётной записью уже зарегистрирован.']]);
            }
        } else {
            // upgrade rules to check user can be created
            $this->rules['email'] = 'nullable|email|required_without_all:user_id,username|unique:users';
            $this->rules['phone'] = 'nullable|unique:users';
            // todo check for NOT email is in username
            $this->rules['username'] = 'nullable|required_without_all:user_id,email|unique:users';
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

        $current = Current::init($request);

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

            if ($user->wasRecentlyCreated) {
                $user->addHistory(HistoryAction::user_created, $current->positionId())->addChanges($userChanges);
            }

            $positionChanges = [];
            $this->set($position, 'user_id', $user->id, Casting::int, $positionChanges);
            $this->set($position, 'status_id', $data['status_id'], Casting::int, $positionChanges);
            $position->history_line_id = $user->history_line_id;
            $position->save();
            $position->roles()->sync($data['roles']);
            $positionChanges[] = ['parameter' => 'roles', 'type' => Casting::array, 'old' => null, 'new' => $data['roles']];
            $position
                ->addHistory(HistoryAction::staff_position_created, $current->positionId())
                ->addChanges($positionChanges)
                ->addLink($user->compactName, EntryScope::user, $user->id);

            $user
                ->addHistory(HistoryAction::user_staff_attached, $current->positionId())
                ->addLink($user->compactName, EntryScope::position, $position->id, PositionType::typeToString(PositionType::staff));
        });

        return APIResponse::success('Сотрудник зарегистрирован')->payload(['id' => $position->id]);
    }
}
