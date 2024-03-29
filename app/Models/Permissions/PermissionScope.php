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

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $scope_name
 * @property string $name
 * @property int $order
 *
 * @property Collection<Permission> $permissions
 */
class PermissionScope extends Model
{
    protected $fillable = [
        'scope_name',
        'name',
    ];

    /**
     * Module's permissions.
     *
     * @return  HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'scope_name', 'scope_name');
    }
}
