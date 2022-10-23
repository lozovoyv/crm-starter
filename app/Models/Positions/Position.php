<?php

namespace App\Models\Positions;

use App\Interfaces\Statusable;
use App\Models\Model;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionRole;
use App\Models\Users\User;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property int $status_id
 * @property int $type_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property PositionType $type
 * @property PositionStatus $status
 * @property User $user
 * @property Collection $roles
 * @property Collection $permissions
 */
class Position extends Model implements Statusable
{
    use HasStatus, HasFactory;

    protected $fillable = ['status_id', 'type_id'];

    /** @var array Default attributes. */
    protected $attributes = [
        'status_id' => PositionStatus::default,
    ];

    /** @var array|null Position permissions cache. */
    protected ?array $permissionsCache = null;

    /**
     * Position's status.
     *
     * @return  HasOne
     */
    public function status(): HasOne
    {
        return $this->hasOne(PositionStatus::class, 'id', 'status_id');
    }

    /**
     * Check and set new status for position.
     *
     * @param int $status
     * @param bool $save
     *
     * @return  void
     */
    public function setStatus(int $status, bool $save = true): void
    {
        $this->checkAndSetStatus(PositionStatus::class, $status, 'status_id', $save);
    }

    /**
     * Position type.
     *
     * @return  HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(PositionType::class, 'id', 'type_id');
    }

    /**
     * CheckRole's permissions.
     *
     * @return  BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'position_has_role', 'position_id', 'role_id')->where('active', true);
    }

    /**
     * CheckRole's permissions.
     *
     * @return  BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'position_has_permission', 'position_id', 'permission_id');
    }

    /**
     * Check position permission.
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

        return in_array($key, $this->getPermissionsList($fresh), true);
    }

    /**
     * Get assigned permissions list.
     *
     * @param bool $fresh
     *
     * @return  array
     */
    public function getPermissionsList(bool $fresh = false): array
    {
        if ($this->permissionsCache === null || $fresh) {
            $this->permissionsCache = [];

            if ($this->roles()->whereIn('id', [PermissionRole::super])->count() > 0) {
                $permissions = Permission::query()->get();
            } else {
                $roles = $this->roles()->with('permissions')->get();
                foreach ($roles as $role) {
                    /** @var PermissionRole $role */
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
        }

        return $this->permissionsCache;
    }

    /**
     * Position related user.
     *
     * @return  HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Check if position has given role.
     *
     * @param int|string $role
     * @param bool $fresh
     *
     * @return  bool
     */
    public function hasRole(int|string $role, bool $fresh = false): bool
    {
        if($fresh) {
            $roles = $this->roles()->get();
        } else {
            $roles = $this->roles;
        }

        foreach ($roles as $role) {
            /** @var PermissionRole $role */
            if ($role->matches($role)) {
                return true;
            }
        }

        return false;
    }
}
