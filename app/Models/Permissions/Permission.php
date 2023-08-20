<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Models\Permissions;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $key
 * @property string $scope_name
 * @property string $name
 * @property string|null $description
 * @property int $order
 *
 * @property PermissionScope $scope
 * @property Collection<PermissionGroup> $groups
 */
class Permission extends Model
{
    protected $fillable = [
        'key',
        'scope_name',
        'name',
    ];

    /**
     * Get permission by key.
     *
     * @param string $key
     *
     * @return Permission|null
     */
    public static function get(string $key): ?Permission
    {
        /** @var Permission|null $permission */
        $permission = self::query()->where('key', $key)->first();

        return $permission;
    }

    /**
     * Permission's module.
     *
     * @return  BelongsTo
     */
    public function scope(): BelongsTo
    {
        return $this->belongsTo(PermissionScope::class, 'scope_name', 'scope_name');
    }

    /**
     * Groups this permission attached to.
     *
     * @return  BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class, 'permission_in_group', 'permission_id', 'group_id');
    }
}
