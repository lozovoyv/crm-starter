<?php
declare(strict_types=1);

namespace App\Models\Users;

use App\Models\AbstractDictionary;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property bool $locked
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserStatus extends AbstractDictionary
{
    public const active = 1;
    public const blocked = 2;

    public const default = self::active;

    /** @var array Attributes casting. */
    protected $casts = [
        'order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @var array Default attributes */
    protected $attributes = [];
}
