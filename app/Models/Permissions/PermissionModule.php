<?php
declare(strict_types=1);

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $module
 * @property string $name
 * @property int $order
 *
 * @property Collection<Permission> $permissions
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
