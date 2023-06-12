<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Resources\Permissions\PermissionGroupEntryResource;
use Exception;
use Illuminate\Http\Request;

class PermissionGroupEditController extends ApiController
{
    /**
     * Get permission group data for edit.
     *
     * @param int|null $groupID
     * @param APIRequest $request
     * @param PermissionGroupEntryResource $resource
     *
     * @return  ApiResponse
     */
    public function get(?int $groupID, APIRequest $request, PermissionGroupEntryResource $resource): ApiResponse
    {
        if ($groupID === null) {
            $groupID = $request->integer('from_group_id');
        }
        $groupID = $groupID ?: null;

        try {
            $group = $resource->get($groupID, null, false, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::form()
            ->title($group->exists ? $group->name : 'Добавление группы прав')
            ->values($resource->getValues($group))
            ->rules($resource->getValidationRules())
            ->titles($resource->getTitles())
            ->messages($resource->getValidationMessages())
            ->hash($resource->getHash($group))
            ->payload([
                'scopes' => $resource->getPermissionsScopes(),
                'permissions' => $resource->getPermissionsIds(),
                'descriptions' => $resource->getPermissionsDescriptions(),
            ]);
    }

    /**
     * Update or create permission group.
     *
     * @param int|null $groupID
     * @param APIRequest $request
     * @param PermissionGroupEntryResource $resource
     *
     * @return  ApiResponse
     *
     * @noinspection DuplicatedCode
     */
    public function save(?int $groupID, APIRequest $request, PermissionGroupEntryResource $resource): ApiResponse
    {
        try {
            $group = $resource->get($groupID, $request->hash(), true, false);
            $data = $resource->filterData($request->data());
            if ($errors = $resource->validate($data, $group)) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $group = $resource->update($group, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success()
            ->message($group->wasRecentlyCreated ? 'Группа прав добавлена' : 'Группа прав сохранена')
            ->payload(['id' => $group->id]);
    }

    /**
     * Change permission group status.
     *
     * @param int $groupID
     * @param APIRequest $request
     * @param PermissionGroupEntryResource $resource
     *
     * @return  ApiResponse
     */
    public function status(int $groupID, APIRequest $request, PermissionGroupEntryResource $resource): ApiResponse
    {
        try {
            $group = $resource->get($groupID, $request->hash(), true);
            $data = $resource->filterData($request->data(), ['active']);
            if ($errors = $resource->validate($data, $group, ['active'])) {
                return APIResponse::validationError($errors);
            }
            $current = Current::init($request);
            $group = $resource->updateStatus($group, $data, $current);

        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::success($group->active ? 'Группа прав включена' : 'Группа прав отключена');
    }

    /**
     * Delete permission group.
     *
     * @param int $groupID
     * @param APIRequest $request
     * @param PermissionGroupEntryResource $resource
     *
     * @return  ApiResponse
     */
    public function remove(int $groupID, APIRequest $request, PermissionGroupEntryResource $resource): ApiResponse
    {
        try {
            $group = $resource->get($groupID, $request->hash(), true);
            $current = Current::init($request);
            $resource->remove($group, $current);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::success('Группа прав удалена');
    }
}
