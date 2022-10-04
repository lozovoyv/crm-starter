<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Current;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionRole;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesController extends ApiController
{
    /**
     * Deactivate role.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function deactivate(Request $request): JsonResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $role->active = false;
        $role->save();

        $current = Current::get($request);
        $role->addHistory(HistoryAction::permission_role_deactivated, $current->positionId());

        return APIResponse::response($role, null, 'Роль отключена');
    }

    /**
     * Activate role.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function activate(Request $request): JsonResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $role->active = true;
        $role->save();

        $current = Current::get($request);
        $role->addHistory(HistoryAction::permission_role_activated, $current->positionId());

        return APIResponse::response($role, null, 'Роль включена');
    }

    /**
     * Delete role.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        try {
            $role->delete();
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить эту роль.');
        }
        
        $current = Current::get($request);
        $role->addHistory(HistoryAction::permission_role_deleted, $current->positionId());

        return APIResponse::success('Роль удалена');
    }

    /**
     * Get role.
     *
     * @param Request $request
     *
     * @return PermissionRole
     * @throws Exception
     */
    protected function getRole(Request $request): PermissionRole
    {
        /** @var PermissionRole|null $role */
        $role = PermissionRole::query()->where('id', $request->input('role_id'))->first();

        if ($role === null) {
            throw new Exception('Роль не найдена');
        }
        if (!$role->isHash($request->input('role_hash'))) {
            throw new Exception('Роль была изменена в другом месте.');
        }
        if ($role->locked) {
            throw new Exception('Эту роль нельзя изменить или удалить');
        }

        return $role;
    }
}
