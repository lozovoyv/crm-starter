<?php
declare(strict_types=1);

namespace App\Models\Positions;

use App\Interfaces\Historical;
use App\Interfaces\Statusable;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Users\User;
use App\Traits\HasHistory;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property int $status_id
 * @property int $type_id
 * @property bool $locked
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property PositionType $type
 * @property PositionStatus $status
 * @property User $user
 * @property Collection<PermissionGroup> $permissionGroups
 * @property Collection<Permission> $permissions
 */
class Position extends Model implements Statusable, Historical
{
    use HasStatus, HasHistory;

    /** @var array Attribute casting. */
    protected $casts = [
        'locked' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
    public function setStatus(int $status, bool $save = false): void
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
     * Position related user.
     *
     * @return  HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault();
    }

    /**
     * Role attached permission groups.
     *
     * @return  BelongsToMany
     */
    public function permissionGroups(): BelongsToMany
    {
        return $this
            ->belongsToMany(PermissionGroup::class, 'position_has_permission_group', 'position_id', 'group_id')
            ->where('active', true);
    }

    /**
     * Role attached permissions.
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

            if ($this->hasType(PositionType::admin)) {
                $permissions = Permission::query()->get();
            } else {
                $groups = $this->permissionGroups()->with('permissions')->get();
                foreach ($groups as $group) {
                    /** @var PermissionGroup $group */
                    foreach ($group->permissions as $permission) {
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
     * Check if position has given role.
     *
     * @param int|string $type
     *
     * @return  bool
     */
    public function hasType(int|string $type): bool
    {
        if (is_int($type)) {
            return $this->type_id === $type;
        }

        return $type === PositionType::typeToString($this->type_id);
    }

    /**
     * History entry caption.
     *
     * @return  string
     */
    public function historyEntryCaption(): string
    {
        $this->loadMissing('user');

        return $this->user->compactName;
    }

    /**
     * History entry tag.
     *
     * @return  string|null
     */
    public function historyEntryTag(): ?string
    {
        return PositionType::typeToString($this->type_id);
    }
}
