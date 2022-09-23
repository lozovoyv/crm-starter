<?php

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $module
 * @property string $name
 * @property int $order
 */
class PermissionModule extends Model
{
    /**
     * Module's permissions.
     *
     * @return  HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'module', 'module');
    }
}
