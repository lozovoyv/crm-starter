<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Actions\Users\UserPasswordChangeAction;
use App\Actions\Users\UserEmailChangeAction;
use App\Actions\Users\UserRemoveAction;
use App\Actions\Users\UserStatusChangeAction;
use App\Actions\Users\UserUpdateAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Resources\Users\UserResource;
use App\VDTO\UserVDTO;
use Exception;

class UserEditController extends ApiController
{
    /**
     * Get user data.
     *
     * @param int|null $userID
     *
     * @return ApiResponse
     */
    public function get(?int $userID = null): ApiResponse
    {

        /** @var User $user */
        try {
            $resource = UserResource::init($userID, null, false, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO();

        $fields = [
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

        return ApiResponse::form()
            ->title($user->exists ? $user->fullName : 'Создание учётной записи')
            ->values($resource->values($fields))
            ->rules($vdto->getValidationRules($fields))
            ->titles($vdto->getTitles($fields))
            ->messages($vdto->getValidationMessages($fields))
            ->hash($resource->getHash($user))
            ->payload(['has_password' => !empty($user->password)]);
    }

    /**
     * Update user data.
     *
     * @param APIRequest $request
     * @param int|null $userID
     *
     * @return  ApiResponse
     */
    public function save(APIRequest $request, ?int $userID = null): ApiResponse
    {

        try {
            $resource = UserResource::init($userID, $request->hash(), true, false);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO(
            $request->data([
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
            ])
        );

        if ($errors = $vdto->validate([], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);
        $user = $resource->user();

        $action = new UserUpdateAction($current);
        $action->execute($user, $vdto);

        $action = new UserStatusChangeAction($current);
        $action->execute($user, $vdto);

        $action = new UserPasswordChangeAction($current);
        $action->execute($user, $data['new_password'] ?? null, $data['clear_password'] ?? false);

        $action = new UserEmailChangeAction($current);
        $action->execute($user, $data['email'] ?? null, $data['email_confirmation_need'] ?? false);

        return APIResponse::success()
            ->message($user->wasRecentlyCreated ? 'Учётная запись добавлена' : 'Учётная запись сохранена')
            ->payload(['id' => $user->id]);
    }

    /**
     * Change user password.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function password(APIRequest $request, int $userID): ApiResponse
    {

        try {
            $resource = UserResource::init($userID, $request->hash(), true);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO($request->data(['new_password', 'clear_password']));

        if ($errors = $vdto->validate(['new_password', 'clear_password'], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new UserPasswordChangeAction($current);
        $action->execute($resource->user(), $vdto);

        return APIResponse::success('Пароль сохранён');
    }

    /**
     * Change user password.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function email(APIRequest $request, int $userID): ApiResponse
    {
        try {
            $resource = UserResource::init($userID, $request->hash(), true);
        } catch (ModelException $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO($request->data(['email', 'email_confirmation_need']));

        if ($errors = $vdto->validate(['email', 'email_confirmation_need'], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new UserEmailChangeAction($current);
        $action->execute($resource->user(), $vdto);

        return APIResponse::success(
            ($data['email_confirmation_need'] ?? false) && !empty($data['email']) ? 'Запрос на подтверждение адреса электронной почты отправлен' : 'Адрес электронной почты сохранён'
        );
    }

    /**
     * Change user status.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function status(APIRequest $request, int $userID): ApiResponse
    {

        try {
            $resource = UserResource::init($userID, $request->hash(), true);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $disabled = $request->input('disabled');
        $statusID = null;
        if ($disabled !== null) {
            $statusID = $disabled ? UserStatus::blocked : UserStatus::active;
        }

        $vdto = new UserVDTO(['status_id' => $statusID]);

        $current = Current::init($request);

        $action = new UserStatusChangeAction($current);
        $action->execute($resource->user(), $vdto);

        return APIResponse::success($resource->user()->hasStatus(UserStatus::active) ? 'Учётная запись активирована' : 'Учётная запись заблокирована');
    }

    /**
     * Delete user.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function remove(APIRequest $request, int $userID): ApiResponse
    {

        $current = Current::init($request);
        $action = new UserRemoveAction($current);

        try {
            $resource = UserResource::init($userID, $request->hash(), true);

            if ($current->userId() === $resource->user()->id) {
                return APIResponse::error('Вы не можете удалить собственную учётную запись');
            }
            $action->execute($resource->user());
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::success('Учётная запись удалена');
    }
}
