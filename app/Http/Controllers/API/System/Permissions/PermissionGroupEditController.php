<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;
use UnexpectedValueException;

class PermissionGroupEditController extends ApiController
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
     * @param int|null $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function get(?int $id, Request $request): ApiResponse
    {
        try {
            $role = $this->getRole($request, false, $request->input('from_role_id'));
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $permissions = [];
        $permissionsIds = [];
        $permissionsDescriptions = [];
        $permissionsModules = [];

        Permission::query()
            ->with(['permissionModule', 'roles' => function (BelongsToMany $query) use ($role) {
                $query->where('id', $role->id);
            }])
            ->orderBy('order')
            ->get()
            ->map(function (Permission $permission) use (&$permissions, &$permissionsIds, &$permissionsDescriptions, &$permissionsModules) {
                $this->titles['permission.' . $permission->id] = $permission->name;
                $permissions['permission.' . $permission->id] = $permission->roles->count() !== 0;
                $permissionsIds[] = $permission->id;
                $permissionsDescriptions[$permission->id] = $permission->description;
                $permissionsModules[$permission->id] = $permission->permissionModule->name;
            });

        return APIResponse::form()
            ->title($role->exists ? $role->name : 'Добавление роли')
            ->values([
                'name' => $role->name,
                'active' => $role->active,
                'description' => $role->description,
                ...$permissions,
            ])
            ->hash($role->getHash())
            ->rules($this->rules)
            ->titles($this->titles)
            ->payload([
                'permissions' => $permissionsIds,
                'descriptions' => $permissionsDescriptions,
                'modules' => $permissionsModules,
            ]);
    }

    /**
     * Update role.
     *
     * @param int|null $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function update(?int $id, Request $request): ApiResponse
    {
        try {
            $role = $this->getRole($request, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);

        $this->rules['name'] = [
            'required',
            Rule::unique('permission_roles', 'name')->ignore($role->id),
        ];

        if ($errors = $this->validate($data, $this->rules, $this->titles)) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

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
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param int|null $overrideId
     *
     * @return PermissionGroup
     */
    protected function getRole(?int $id, ?string $hash, bool $check, ?int $overrideId = null): PermissionGroup
    {
        $roleId = $request->input('role_id');

        /** @var PermissionGroup|null $role */
        if ($roleId === 0) {
            if ($overrideId !== null) {
                $role = PermissionGroup::query()->where('id', $overrideId)->first();
                if ($role) {
                    $role->exists = false;
                }
            } else {
                $role = new PermissionGroup();
            }
        } else {
            $role = PermissionGroup::query()->where('id', $request->input('role_id'))->first();
        }

        if ($role === null) {
            throw new UnexpectedValueException('Роль не найдена');
        }
        if ($check && $role->exists && !$role->isHash($request->input('hash'))) {
            throw new RuntimeException('Роль была изменена в другом месте.');
        }
        if ($role->locked) {
            throw new RuntimeException('Эту роль нельзя изменить или удалить');
        }

        return $role;
    }
}
