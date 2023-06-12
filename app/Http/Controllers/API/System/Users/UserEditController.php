<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Resources\Users\UserResource;
use Exception;

class UserEditController extends ApiController
{
    /**
     * Get user data.
     *
     * @param int|null $userID
     * @param UserResource $resource
     *
     * @return ApiResponse
     */
    public function get(?int $userID, UserResource $resource): ApiResponse
    {
        /** @var User $user */
        try {
            $user = $resource->get($userID, null, false, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::form()
            ->title($user->exists ? $user->fullName : 'Создание учётной записи')
            ->values($resource->getValues($user))
            ->rules($resource->getValidationRules())
            ->titles($resource->getTitles())
            ->messages($resource->getValidationMessages())
            ->hash($resource->getHash($user))
            ->payload(['has_password' => !empty($user->password)]);
    }

    /**
     * Update user data.
     *
     * @param int|null $userID
     * @param APIRequest $request
     * @param UserResource $resource
     *
     * @return  ApiResponse
     * @noinspection DuplicatedCode
     */
    public function save(?int $userID, APIRequest $request, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID, $request->hash(), true, false);
            $data = $resource->filterData($request->data());
            if ($errors = $resource->validate($data, $user)) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $user = $resource->update($user, $data, $current);
            $user = $resource->updateStatus($user, $data, $current);
            $user = $resource->updatePassword($user, $data, $current);
            $user = $resource->updateEmail($user, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success()
            ->message($user->wasRecentlyCreated ? 'Учётная запись добавлена' : 'Учётная запись сохранена')
            ->payload(['id' => $user->id]);
    }

    /**
     * Change user password.
     *
     * @param int $userID
     * @param APIRequest $request
     * @param UserResource $resource
     *
     * @return  ApiResponse
     */
    public function password(int $userID, APIRequest $request, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID, $request->hash(), true);
            $data = $resource->filterData($request->data(), ['new_password', 'clear_password']);
            if ($errors = $resource->validate($data, $user, ['new_password', 'clear_password'])) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $resource->updatePassword($user, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success('Пароль сохранён');
    }

    /**
     * Change user password.
     *
     * @param int $userID
     * @param APIRequest $request
     * @param UserResource $resource
     *
     * @return  ApiResponse
     */
    public function email(int $userID, APIRequest $request, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID, $request->hash(), true);
            $data = $resource->filterData($request->data(), ['email', 'email_confirmation_need']);
            if ($errors = $resource->validate($data, $user, ['email', 'email_confirmation_need'])) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $resource->updateEmail($user, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success(
            ($data['email_confirmation_need'] ?? false) && !empty($data['email']) ? 'Запрос на подтверждение адреса электронной почты отправлен' : 'Адрес электронной почты сохранён'
        );
    }

    /**
     * Change user status.
     *
     * @param int $userID
     * @param APIRequest $request
     * @param UserResource $resource
     *
     * @return  ApiResponse
     */
    public function status(int $userID, APIRequest $request, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID, $request->hash(), true);
            $data = $resource->filterData($request->data(), ['status_id']);
            if ($errors = $resource->validate($data, $user, ['status_id'])) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $user = $resource->updateStatus($user, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success($user->hasStatus(UserStatus::active) ? 'Учётная запись активирована' : 'Учётная запись заблокирована');
    }

    /**
     * Delete user.
     *
     * @param int $userID
     * @param APIRequest $request
     * @param UserResource $resource
     *
     * @return  ApiResponse
     */
    public function remove(int $userID, APIRequest $request, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID, $request->hash(), true);
            $current = Current::init($request);
            if ($current->userId() === $user->id) {
                return APIResponse::error('Вы не можете удалить собственную учётную запись');
            }
            $resource->remove($user, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::success('Учётная запись удалена');
    }
}
