<?php

namespace App\Models\Positions;

use App\Foundation\Dictionaries\AbstractDictionary;
use InvalidArgumentException;

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

    public static function typeToString(int $type): string
    {
        switch ($type) {
            case 1:
                return 'staff';
        }

        throw new InvalidArgumentException('Wrong position type');
    }
}
