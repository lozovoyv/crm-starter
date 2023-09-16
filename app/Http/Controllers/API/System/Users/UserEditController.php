<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Resources\Users\UserEntryResource;
use Exception;

class UserEditController extends ApiController
{
    /**
     * Get user data.
     *
     * @param UserEntryResource $resource
     * @param int|null $userID
     *
     * @return ApiResponse
     */
    public function get(UserEntryResource $resource, ?int $userID = null): ApiResponse
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
     * @param UserEntryResource $resource
     * @param APIRequest $request
     * @param int|null $userID
     *
     * @return  ApiResponse
     */
    public function save(UserEntryResource $resource, APIRequest $request, ?int $userID = null): ApiResponse
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
     * @param UserEntryResource $resource
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function password(UserEntryResource $resource, APIRequest $request, int $userID): ApiResponse
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
     * @param UserEntryResource $resource
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function email(UserEntryResource $resource, APIRequest $request, int $userID): ApiResponse
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
     * @param UserEntryResource $resource
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function status(UserEntryResource $resource, APIRequest $request, int $userID): ApiResponse
    {
        try {
            $disabled = $request->input('disabled');
            $data['status_id'] = null;
            if ($disabled !== null) {
                $data['status_id'] = $disabled ? UserStatus::blocked : UserStatus::active;
            }
            $user = $resource->get($userID, $request->hash(), true);
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
     * @param UserEntryResource $resource
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function remove(UserEntryResource $resource, APIRequest $request, int $userID): ApiResponse
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
