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
use App\Models\EntryScope;
use App\Models\Model;
use App\Traits\HashCheck;
use App\Traits\HasSimpleHistory;
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
    use HashCheck, HasSimpleHistory, SetAttributeWithChanges;

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
        return EntryScope::permission_group;
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
        return $this->belongsToMany(Permission::class, 'permission_in_group', 'group_id', 'permission_id');
    }
}
