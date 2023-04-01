<?php
declare(strict_types=1);

namespace App\Models\Permissions;

use App\Interfaces\Historical;
use App\Models\EntryScope;
use App\Models\Model;
use App\Traits\HasSimpleHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property bool $locked
 * @property Collection<Permission> $permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PermissionRole extends Model implements Historical
{
    use HasSimpleHistory;

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
     * History entry title.
     *
     * @return  string
     */
    public function historyEntryTitle(): string
    {
        return $this->name;
    }

    /**
     * History entry name.
     *
     * @return  string
     */
    public function historyEntryName(): string
    {
        return EntryScope::role;
    }

    /**
     * History entry name.
     *
     * @return  string|null
     */
    public function historyEntryType(): ?string
    {
        return null;
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
     * Default representation role as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        $permissionsCount = $this->id === self::super
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
