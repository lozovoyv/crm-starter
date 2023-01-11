<?php

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Foundation\Casting;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends ApiController
{
    /**
     * Deactivate user.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function deactivate(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::get($request);

        if ($current->userId() === $user->id) {
            return APIResponse::error('Вы не можете заблокировать собственную учётную запись');
        }

        $user->setStatus(UserStatus::blocked, true);

        $user->addHistory(HistoryAction::user_deactivated, $current->positionId());

        return APIResponse::response($user, null, 'Учётная запись заблокирована');
    }

    /**
     * Activate user.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function activate(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $user->setStatus(UserStatus::active, true);

        $current = Current::get($request);
        $user->addHistory(HistoryAction::user_activated, $current->positionId());

        return APIResponse::response($user, null, 'Учётная запись активирована');
    }

    /**
     * Delete user.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $current = Current::get($request);

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
            DB::transaction(function () use ($user, $changes, $current) {
                $user
                    ->addHistory(HistoryAction::user_deleted, $current->positionId())
                    ->addChanges($changes);

                $user->delete();
            });
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить учётную запись.');
        }

        return APIResponse::success('Учётная запись удалена');
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
            throw new Exception('Учётная запись не найдена');
        }
        if (!$user->isHash($request->input('user_hash'))) {
            throw new Exception('Учётная запись была изменена в другом месте.');
        }

        return $user;
    }
}
