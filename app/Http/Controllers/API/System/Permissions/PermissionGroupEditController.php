<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Actions\Permission\PermissionGroupUpdateAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Permissions\PermissionGroupResource;
use App\VDTO\PermissionGroupVDTO;

class PermissionGroupEditController extends ApiController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__permissions),
        ]);
    }

    /**
     * Get permission group data for edit.
     *
     * @param APIRequest $request
     * @param int|null $groupID
     *
     * @return  ApiResponse
     */
    public function get(APIRequest $request, ?int $groupID = null): ApiResponse
    {
        $fromGroupID = $request->integer('from_group_id');

        try {
            $resource = PermissionGroupResource::get($groupID ?? $fromGroupID, null, false, false);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new PermissionGroupVDTO();

        $fields = [
            'name',
            'active',
            'description',
        ];

        $titles = array_merge(
            $vdto->getTitles($fields),
            $resource->getPermissionsNames()
        );

        $values = array_merge(
            $resource->values($fields),
            $resource->getPermissionsValues()
        );

        return ApiResponse::form()
            ->title($groupID ? $resource->group()->name : 'Создание группы прав')
            ->values($values)
            ->rules($vdto->getValidationRules($fields))
            ->titles($titles)
            ->messages($vdto->getValidationMessages($fields))
            ->hash($resource->getHash())
            ->payload([
                'scopes' => $resource->getPermissionsScopes(),
                'descriptions' => $resource->getPermissionsDescriptions(),
            ]);
    }

    /**
     * Update or create permission group.
     *
     * @param int|null $groupID
     * @param APIRequest $request
     *
     * @return  ApiResponse
     */
    public function put(APIRequest $request, ?int $groupID = null): ApiResponse
    {
        try {
            $resource = PermissionGroupResource::get($groupID, $request->hash(), true, false);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new PermissionGroupVDTO($request->data());

        if ($errors = $vdto->validate([], $resource->group())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new PermissionGroupUpdateAction($current);
        $action->execute($resource->group(), $vdto);

        return APIResponse::success()
            ->message($resource->group()->wasRecentlyCreated ? 'Группа прав добавлена' : 'Группа прав сохранена')
            ->payload(['id' => $resource->group()]);
    }
}
