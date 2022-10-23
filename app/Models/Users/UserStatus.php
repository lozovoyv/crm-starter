<?php

namespace App\Models\Users;

use App\Foundation\Dictionaries\AbstractDictionary;
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

    /**
     * Cast to array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enabled' => $this->enabled,
        ];
    }
}
