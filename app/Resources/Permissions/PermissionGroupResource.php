<?php
declare(strict_types=1);

namespace App\Resources\Permissions;

use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\Model\ModelWrongHashException;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Resources\EntryResource;
use Illuminate\Database\Eloquent\Collection;

class PermissionGroupResource extends EntryResource
{
    protected PermissionGroup $group;

    /** @var Collection<Permission> */
    protected Collection $permissions;

    public function __construct(PermissionGroup $group)
    {
        $this->group = $group;
        $this->permissions = Permission::query()->withScope()->order('name')->get();
        $this->formTitle = $group->exists ? $group->name : 'permissions/permission_group.new_group';
    }

    public function group(): PermissionGroup
    {
        return $this->group;
    }

    /**
     * Get permission group.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $checkHash
     * @param bool $onlyExisting
     *
     * @return $this
     *
     * @throws ModelLockedException
     * @throws ModelWrongHashException
     * @throws ModelNotFoundException
     */
    public static function get(?int $id, ?string $hash = null, bool $checkHash = false, bool $onlyExisting = true): self
    {
        /** @var PermissionGroup|null $group */
        if ($id === null) {
            $group = $onlyExisting ? null : new PermissionGroup();
        } else {
            $group = PermissionGroup::query()->where('id', $id)->first();
        }

        if ($group === null) {
            throw new ModelNotFoundException('permissions/permission_group.model_not_found_exception');
        }
        if ($checkHash && $group->exists && !$group->isHash($hash)) {
            throw new ModelWrongHashException('permissions/permission_group.model_wrong_hash_exception');
        }
        if ($checkHash && $group->locked) {
            throw new ModelLockedException('permissions/permission_group.model_locked_exception');
        }

        return new static($group);
    }

    /**
     * Get permission group hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->group->hash();
    }

    /**
     * Get formatted fields values
     *
     * @param array $fields
     *
     * @return array
     */
    public function values(array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            if ($field === 'permissions') {
                $values[$field] = $this->group->permissions->pluck('id')->toArray();
            } else {
                $values[$field] = $this->group->getAttribute($field);
            }
        }

        return $values;
    }

    /**
     * Get scopes names permissions belongs to.
     *
     * @return array
     */
    public function getPermissionsScopes(): array
    {
        $scopes = [];

        $this->permissions->map(function (Permission $permission) use (&$scopes) {
            if (!isset($scopes[$permission->scope->id])) {
                $scopes[$permission->scope->id] = [
                    'name' => $permission->scope->name,
                    'permissions' => [],
                ];
            }
            $scopes[$permission->scope->id]['permissions'][] = $permission->id;
        });

        return $scopes;
    }

    /**
     * Get all permissions names.
     *
     * @return array
     */
    public function getPermissionsNames(): array
    {
        return $this->permissions->mapWithKeys(function (Permission $permission) {
            return ['permission.' . $permission->id => $permission->name];
        })->toArray();
    }

    /**
     *  Get all permissions descriptions.
     *
     * @return array
     */
    public function getPermissionsDescriptions(): array
    {
        return $this->permissions->mapWithKeys(function (Permission $permission) {
            return [$permission->id => $permission->description];
        })->toArray();
    }
}
