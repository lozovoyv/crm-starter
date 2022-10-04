<?php

namespace App\Models\Positions;

use App\Foundation\Dictionaries\AbstractDictionary;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property int $order
 */
class PositionStatus extends AbstractDictionary
{
    /** @var int The id of active status */
    public const active = 1;

    /** @var int The id of blocked status */
    public const blocked = 2;

    /** @var int Default status */
    public const default = self::active;
}
