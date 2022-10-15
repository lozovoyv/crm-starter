<?php

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $key
 * @property string $module
 * @property string $name
 * @property string|null $description
 * @property int $order
 *
 * @property PermissionModule $permissionModule
 * @property Collection $roles
 */
class Permission extends Model
{
    /**
     * Permission's module.
     *
     * @return  BelongsTo
     */
    public function permissionModule(): BelongsTo
    {
        return $this->belongsTo(PermissionModule::class, 'module', 'module');
    }

    /**
     * Roles this permission attached to.
     *
     * @return  BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'permission_in_role', 'permission_id', 'role_id');
    }

    /**
     * Cast permission as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'module' => $this->permissionModule->name,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
