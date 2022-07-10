<?php

namespace App\Traits;

use App\Models\Model;
use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @mixin  Model
 */
class HasPermissions
{
    /** @var array|null Permissions cache. */
    private ?array $permissionsCache = null;

    // For non-standard names variables could be defined manually:
    // protected string $rolePivotTable = 'position_has_role';
    // protected string $rolePivotColumn = 'position_id';
    // protected string $permissionsPivotTable = 'position_has_permission';
    // protected string $permissionsPivotColumn = 'position_id';

    /**
     * Get definition.
     *
     * @param string $var
     * @param string $postfix
     *
     * @return  string
     */
    private function getName(string $var, string $postfix):string
    {
        return $this->$var ?? (Str::snake(static::class) . $postfix); // TODO test this
    }

    /**
     * Entry's permissions.
     *
     * @return  BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            $this->getName('rolePivotTable', '_has_role'),
            $this->getName('rolePivotColumn', '_id'),
            'role_id'
        )->where('active', true);
    }

    /**
     * Entry's permissions.
     *
     * @return  BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            $this->getName('permissionsPivotTable', '_has_permission'),
            $this->getName('permissionsPivotColumn', '_id'),
            'permission_id'
        );
    }


    /**
     * Get assigned permissions list.
     *
     * @param bool $fresh
     *
     * @return  array
     */
    public function permissionsList(bool $fresh = false): array
    {
        if ($this->permissionsCache !== null && !$fresh) {
            return $this->permissionsCache;
        }

        $this->permissionsCache = [];

        if ($this->roles()->whereIn('id', [Role::super])->count() > 0) {
            $permissions = Permission::query()->get();
        } else {
            $roles = $this->roles()->with('permissions')->get();
            foreach ($roles as $role) {
                /** @var Role $role */
                foreach ($role->permissions as $permission) {
                    /** @var Permission $permission */
                    $this->permissionsCache[$permission->id] = $permission->key;
                }
            }
            $permissions = $this->permissions()->get();
        }

        foreach ($permissions as $permission) {
            /** @var Permission $permission */
            $this->permissionsCache[$permission->id] = $permission->key;
        }

        return $this->permissionsCache;
    }

    /**
     * Check permission.
     *
     * @param string|null $key
     * @param bool $fresh
     *
     * @return  bool
     */
    public function can(?string $key, bool $fresh = false): bool
    {
        if (empty($key)) {
            return true;
        }

        if ($this->roles()->whereIn('id', [Role::super])->count() > 0) {
            return true;
        }

        return in_array($key, $this->permissionsList($fresh), true);
    }
}
