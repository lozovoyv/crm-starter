<?php

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $key
 * @property string $module
 * @property string $name
 * @property string|null $description
 * @property int $order
 *
 * @property PermissionModule $permissionModule
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
     * Cast permission as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'module' => $this->permissionModule->name,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
