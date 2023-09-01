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

use App\Interfaces\HashCheckable;
use App\Interfaces\Historical;
use App\Models\Model;
use App\Traits\HashCheck;
use App\Traits\HasHistory;
use App\Traits\SetAttributeWithChanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property bool $locked
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection<Permission> $permissions
 */
class PermissionGroup extends Model implements Historical, HashCheckable
{
    use HashCheck, HasHistory, SetAttributeWithChanges;

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
    public function historyEntryCaption(): string
    {
        return $this->name;
    }

    /**
     * CheckRole's permissions.
     *
     * @return  BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_in_group', 'group_id', 'permission_id');
    }
}
