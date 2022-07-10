<?php

namespace App\Models\Dictionaries;

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

    /** @var string Referenced table name. */
    protected $table = 'dictionary_user_statuses';
}
