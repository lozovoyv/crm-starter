<?php

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $active
 * @property bool $locked
 * @property Collection $permissions
 */
class PermissionRole extends Model
{
    /** @var int Id for super-admin role */
    public const super = 1;

    /** @var array Attributes casting. */
    protected $casts = [
        'active' => 'bool',
        'locked' => 'bool',
    ];

    /** @var array Default attributes. */
    protected $attributes = [
        'locked' => false,
        'active' => true,
    ];

    /**
     * Role's permissions.
     *
     * @return  BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_in_role', 'role_id', 'permission_id');
    }
}
