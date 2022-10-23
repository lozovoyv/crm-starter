<?php

namespace App\Models\Permissions;

use App\Models\History\HistoryScope;
use App\Models\Model;
use App\Traits\HashCheck;
use App\Traits\HasHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $active
 * @property bool $locked
 * @property Collection $permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PermissionRole extends Model
{
    use HashCheck, HasHistory;

    /** @var int Id for super-admin role */
    public const super = 1;

    /** @var array Attributes casting. */
    protected $casts = [
        'active' => 'bool',
        'locked' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @var array Default attributes. */
    protected $attributes = [
        'locked' => false,
        'active' => true,
    ];

    /**
     * History entry name.
     *
     * @return  string
     */
    protected function historyEntryName(): string
    {
        return HistoryScope::role;
    }

    /**
     * CheckRole's permissions.
     *
     * @return  BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_in_role', 'role_id', 'permission_id');
    }

    /**
     * Instance hash.
     *
     * @return  string|null
     */
    protected function hash(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Check this role matches given.
     *
     * @param int|string $role
     *
     * @return  bool
     */
    public function matches(int|string $role): bool
    {
        if (is_string($role)) {
            $roleId = constant('self::' . $role);
        } else {
            $roleId = $role;
        }

        return $this->id === $roleId;
    }

    /**
     * Cast role as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        $permissionsCount = $this->id === PermissionRole::super
            ? Permission::query()->count()
            : $this->getAttribute('permissions_count') ?? $this->permissions()->count();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $permissionsCount,
            'description' => $this->description,
            'active' => $this->active,
            'locked' => $this->locked,
            'hash' => $this->getHash(),
            'updated_at' => $this->updated_at,
        ];
    }
}
