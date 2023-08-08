<?php
declare(strict_types=1);

namespace App\Resources\Permissions;

use App\Current;
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\Model\ModelWrongHashException;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChanges;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Resources\EntryResource;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionGroupEntryResource extends EntryResource
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
     * Get permission group.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param bool $onlyExisting
     *
     * @return PermissionGroup
     *
     * @throws ModelLockedException
     * @throws ModelWrongHashException
     * @throws ModelNotFoundException
     */
    public function get(?int $id, ?string $hash = null, bool $check = false, bool $onlyExisting = true): PermissionGroup
    {
        /** @var PermissionGroup|null $group */
        if ($id === null) {
            $group = $onlyExisting ? null : new PermissionGroup();
        } else {
            $group = PermissionGroup::query()->where('id', $id)->first();
        }

        if ($group === null) {
            throw new ModelNotFoundException('Группа прав не найдена.');
        }
        if ($check && $group->exists && !$group->isHash($hash)) {
            throw new ModelWrongHashException('Группа прав была изменена в другом месте.');
        }
        if ($check && $group->locked) {
            throw new ModelLockedException('Эту группу прав нельзя изменить или удалить.');
        }

        return $group;
    }

    /**
     * Get permission group hash.
     *
     * @param PermissionGroup $group
     *
     * @return string|null
     */
    public function getHash(PermissionGroup $group): ?string
    {
        return $group->getHash();
    }

    /**
     * Get scopes names permissions belongs to.
     *
     * @return array
     */
    public function getPermissionsScopes(): array
    {
        $permissions = Permission::query()->with('scope')->orderBy('order')->get();
        $permissions->keyBy('id');

        return $permissions->pluck('scope.name')->toArray();
    }

    /**
     * Get all permissions ids.
     *
     * @return array
     */
    public function getPermissionsIds(): array
    {
        return Permission::query()->orderBy('order')->pluck('id')->toArray();
    }

    /**
     *  Get all permissions descriptions.
     *
     * @return array
     */
    public function getPermissionsDescriptions(): array
    {
        $permissions = Permission::query()->orderBy('order')->get();
        $permissions->keyBy('id');

        return $permissions->pluck('description')->toArray();
    }

    /**
     * Get data from request.
     *
     * @param array $data
     * @param array $only
     *
     * @return  array
     */
    public function filterData(array $data, array $only = []): array
    {
        if (empty($only)) {
            $only = [
                'name',
                'active',
                'description',
            ];
            foreach ($this->getPermissionsIds() as $existingID) {
                $only[] = 'permission.' . $existingID;
            }
        }

        return parent::filterData($data, $only);
    }

    /**
     * Get fields titles.
     *
     * @return array
     */
    public function getTitles(): array
    {
        $titles = $this->titles;

        $permissions = Permission::query()
            ->orderBy('order')
            ->get();

        $permissions->map(function (Permission $permission) use (&$titles) {
            $titles['permission.' . $permission->id] = $permission->name;
        });

        return $titles;
    }

    /**
     * Get formatted fields values for user
     *
     * @param PermissionGroup $group
     *
     * @return array
     */
    public function getValues(PermissionGroup $group): array
    {
        $values = [
            'name' => $group->name,
            'active' => $group->active,
            'description' => $group->description,
        ];

        $permissions = Permission::query()
            ->with(['groups' => function (BelongsToMany $query) use ($group) {
                $query->where('id', $group->id);
            }])
            ->orderBy('order')
            ->get();

        $permissions->map(function (Permission $permission) use (&$values) {
            $values['permission.' . $permission->id] = $permission->groups->count() !== 0;
        });

        return $values;
    }

    /**
     * Validate data and return validation errors.
     *
     * @param array $data
     * @param PermissionGroup $group
     * @param array $only
     *
     * @return  array|null
     */
    public function validate(array $data, PermissionGroup $group, array $only = []): ?array
    {
        $rules = $this->rules;
        $rules['name'] = ['required', Rule::unique('permission_groups', 'name')->ignore($group->id)];

        if (!empty($only)) {
            $rules = Arr::only($rules, $only);
        }

        return $this->validateData($data, $rules, $this->titles, $this->messages);
    }

    /**
     * Update user data.
     *
     * @param PermissionGroup $group
     * @param array $data
     * @param Current $current
     *
     * @return PermissionGroup
     */
    public function update(PermissionGroup $group, array $data, Current $current): PermissionGroup
    {
        DB::transaction(function () use (&$group, $data, $current) {
            $changes = [];
            $changes[] = $group->setAttributeWithChanges('name', $data['name'], Casting::string);
            $changes[] = $group->setAttributeWithChanges('active', $data['active'], Casting::bool);
            $changes[] = $group->setAttributeWithChanges('description', $data['description'], Casting::string);
            $group->save();

            $ids = [];

            $existingIds = $this->getPermissionsIds();
            $oldIds = $group->permissions()->pluck('id')->toArray();

            foreach ($existingIds as $existingId) {
                if (!empty($data['permission.' . $existingId])) {
                    $ids[] = $existingId;
                }
            }

            sort($ids);
            sort($oldIds);

            $changed = $group->permissions()->sync($ids);

            if (count($changed['attached']) || count($changed['updated']) || count($changed['detached'])) {
                $group->touch();
                $changes[] = new HistoryChanges(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $oldIds, 'new' => $ids]);
            }

            $changes = array_filter($changes);

            if (!empty($changes)) {
                $group
                    ->addHistory($group->wasRecentlyCreated ? HistoryAction::permission_group_created : HistoryAction::permission_group_edited, $current->positionId())
                    ->addChanges($changes);
            }
        });

        return $group;
    }

    /**
     * Change user status.
     *
     * @param PermissionGroup $group
     * @param array $data
     * @param Current $current
     *
     * @return PermissionGroup
     */
    public function updateStatus(PermissionGroup $group, array $data, Current $current): PermissionGroup
    {
        if (!array_key_exists('active', $data)) {
            return $group;
        }

        if ($data['active'] !== $group->active) {
            $group->active = $data['active'];
            $group->save();

            $action = $group->active ? HistoryAction::permission_group_activated : HistoryAction::permission_group_deactivated;
            $group->addHistory($action, $current->positionId());
        }

        return $group;
    }

    /**
     * Remove user.
     *
     * @param PermissionGroup $group
     * @param Current $current
     *
     * @return void
     *
     * @throws ModelDeleteBlockedException
     */
    public function remove(PermissionGroup $group, Current $current): void
    {
        $changes = [
            new HistoryChanges(['parameter' => 'name', 'type' => Casting::string, 'old' => $group->name, 'new' => null]),
            new HistoryChanges(['parameter' => 'active', 'type' => Casting::bool, 'old' => $group->active, 'new' => null]),
            new HistoryChanges(['parameter' => 'description', 'type' => Casting::string, 'old' => $group->description, 'new' => null]),
            new HistoryChanges(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $group->permissions()->pluck('id')->toArray(), 'new' => null]),
        ];

        try {
            DB::transaction(static function () use ($group, $changes, $current) {
                $group
                    ->addHistory(HistoryAction::permission_group_deleted, $current->positionId())
                    ->addChanges($changes);

                $group->delete();
            });
        } catch (QueryException) {
            throw new ModelDeleteBlockedException('Невозможно удалить группу прав');
        }
    }
}
