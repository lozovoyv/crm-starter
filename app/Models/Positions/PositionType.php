<?php

namespace App\Models\Positions;

use App\Foundation\Dictionaries\AbstractDictionary;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property int $order
 */
class PositionType extends AbstractDictionary
{
    /** @var int The id of staff position type */
    public const staff = 1;
}
