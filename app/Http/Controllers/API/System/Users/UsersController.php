<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Utils\Casting;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use UnexpectedValueException;

class UsersController extends ApiController
{
    /**
     * Deactivate user.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function deactivate(Request $request): ApiResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::init($request);

        if ($current->userId() === $user->id) {
            return APIResponse::error('Вы не можете заблокировать собственную учётную запись');
        }

        $user->setStatus(UserStatus::blocked, true);

        $user->addHistory(HistoryAction::user_deactivated, $current->positionId());

        return ApiResponse::success('Учётная запись заблокирована');
    }

    /**
     * Activate user.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function activate(Request $request): ApiResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $user->setStatus(UserStatus::active, true);

        $current = Current::init($request);
        $user->addHistory(HistoryAction::user_activated, $current->positionId());

        return APIResponse::success('Учётная запись активирована');
    }

    /**
     * Change user password.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function password(Request $request): ApiResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);

        if ($errors = $this->validate($data, ['password' => 'nullable|min:6'], ['password' => 'Пароль'])) {
            return APIResponse::validationError($errors);
        }

        $hadPassword = !empty($user->password);
        $hasPassword = !empty($data['password']);

        $user->password = empty($data['password']) ? null : Hash::make($data['password']);
        $user->save();

        $current = Current::init($request);

        if ($hadPassword) {
            $user->addHistory($hasPassword ? HistoryAction::user_password_changed : HistoryAction::user_password_cleared, $current->positionId());
        } else if ($hasPassword) {
            $user->addHistory(HistoryAction::user_password_set, $current->positionId());
        }

        return APIResponse::success($hasPassword !== $hadPassword ? 'Пароль изменён' : 'Нет изменений');
    }

    /**
     * Delete user.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function remove(Request $request): ApiResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::init($request);

        if ($current->userId() === $user->id) {
            return APIResponse::error('Вы не можете удалить собственную учётную запись');
        }

        $changes = [
            ['parameter' => 'lastname', 'type' => Casting::string, 'old' => $user->lastname, 'new' => null],
            ['parameter' => 'firstname', 'type' => Casting::string, 'old' => $user->firstname, 'new' => null],
            ['parameter' => 'patronymic', 'type' => Casting::string, 'old' => $user->patronymic, 'new' => null],
            ['parameter' => 'display_name', 'type' => Casting::string, 'old' => $user->display_name, 'new' => null],
            ['parameter' => 'email', 'type' => Casting::string, 'old' => $user->email, 'new' => null],
            ['parameter' => 'phone', 'type' => Casting::string, 'old' => $user->phone, 'new' => null],
            ['parameter' => 'username', 'type' => Casting::string, 'old' => $user->username, 'new' => null],
        ];

        try {
            DB::transaction(static function () use ($user, $changes, $current) {
                $user
                    ->addHistory(HistoryAction::user_deleted, $current->positionId())
                    ->addChanges($changes);

                $user->delete();
            });
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить учётную запись.');
        }

        return ApiResponse::success('Учётная запись удалена');
    }

    /**
     * Get user.
     *
     * @param Request $request
     *
     * @return User
     * @throws Exception
     */
    protected function getUser(Request $request): User
    {
        /** @var User|null $user */
        $user = User::query()->where('id', $request->input('user_id'))->first();

        if ($user === null) {
            throw new UnexpectedValueException('Учётная запись не найдена');
        }
        if (!$user->isHash($request->input('user_hash'))) {
            throw new RuntimeException('Учётная запись была изменена в другом месте.');
        }

        return $user;
    }
}
