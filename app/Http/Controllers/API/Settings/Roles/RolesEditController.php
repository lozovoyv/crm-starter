<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Current;
use App\Foundation\Casting;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\History\HistoryChanges;
use App\Models\Permissions\Permission;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionRole;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesEditController extends ApiController
{
    protected array $rules = [
        'name' => 'required',
        'active' => 'required',
        'description' => 'nullable',
    ];

    protected array $titles = [
        'name' => 'Название',
        'active' => 'Статус',
        'description' => 'Описание',
    ];

    /**
     * Get role data.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        try {
            $role = $this->getRole($request, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $permissions = [];
        $permissionsIds = [];

        Permission::query()
            ->with(['permissionModule', 'roles' => function (BelongsToMany $query) use ($role) {
                $query->where('id', $role->id);
            }])
            ->get()
            ->map(function (Permission $permission) use (&$permissions, &$permissionsIds) {
                $this->titles['permission.' . $permission->id] = $permission->permissionModule->name . ' — ' . $permission->name;
                $permissions['permission.' . $permission->id] = $permission->roles->count() !== 0;
                $permissionsIds[] = $permission->id;
            });

        return APIResponse::form(
            $role->exists ? $role->name : 'Добавление роли',
            [
                'name' => $role->name,
                'active' => $role->active,
                'description' => $role->description,
                ...$permissions,
            ],
            $role->getHash(),
            $this->rules,
            $this->titles,
            [
                'permissions' => $permissionsIds,
            ]
        );
    }

    /**
     * Update role.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $role = $this->getRole($request, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);

        if ($errors = $this->validate($data, $this->rules, $this->titles)) {
            return APIResponse::validationError($errors);
        }

        $current = Current::get($request);

        $hasChanges = false;

        DB::transaction(function () use ($role, $data, $current, &$hasChanges) {
            $changes = [];
            $this->set($role, 'name', $data['name'], Casting::string, $changes);
            $this->set($role, 'active', $data['active'], Casting::bool, $changes);
            $this->set($role, 'description', $data['description'], Casting::string, $changes);
            $role->save();

            $permissionsIds = [];

            Permission::query()->get()
                ->map(function (Permission $permission) use ($data, &$permissionsIds) {
                    if (!empty($data['permission.' . $permission->id])) {
                        $permissionsIds[] = $permission->id;
                    }
                });

            $oldPermissions = $role->permissions()->pluck('id')->toArray();

            sort($permissionsIds);
            sort($oldPermissions);

            $permissionChanges = $role->permissions()->sync($permissionsIds);

            if (count($permissionChanges['attached']) || count($permissionChanges['updated']) || count($permissionChanges['detached'])) {
                $role->touch();
                $changes[] = ['parameter' => 'permissions', 'type' => Casting::array, 'old' => $oldPermissions, 'new' => $permissionsIds];
            }

            if (!empty($changes)) {
                $role
                    ->addHistory($role->wasRecentlyCreated ? HistoryAction::permission_role_created : HistoryAction::permission_role_edited, $current->positionId())
                    ->addLink($role->name)
                    ->addChanges($changes);
                $hasChanges = true;
            }
        });

        if (!$hasChanges) {
            return APIResponse::success('Изменений не сделано');
        }

        return APIResponse::success($role->wasRecentlyCreated ? 'Роль добавлена' : 'Роль сохранена');
    }

    /**
     * Get role.
     *
     * @param Request $request
     * @param bool $check
     *
     * @return PermissionRole
     * @throws Exception
     */
    protected function getRole(Request $request, bool $check): PermissionRole
    {
        $roleId = $request->input('role_id');

        /** @var PermissionRole|null $role */
        $role = $roleId === 0 ? new PermissionRole() : PermissionRole::query()->where('id', $request->input('role_id'))->first();

        if ($role === null) {
            throw new Exception('Роль не найдена');
        }
        if ($check && !$role->isHash($request->input('hash'))) {
            throw new Exception('Роль была изменена в другом месте.');
        }
        if ($role->locked) {
            throw new Exception('Эту роль нельзя изменить или удалить');
        }

        return $role;
    }
}
