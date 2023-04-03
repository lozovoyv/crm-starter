<?php
declare(strict_types=1);

namespace App\Models\Positions;

use App\Models\AbstractDictionary;
use Database\Seeders\Seeders\PositionTypesSeeder;
use InvalidArgumentException;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property int $order
 *
 * @see PositionTypesSeeder
 */
class PositionType extends AbstractDictionary
{
    /** @var int The id of staff position type */
    public const admin = 1;

    /** @var int The id of staff position type */
    public const staff = 2;

    public static function typeToString(int $type): string
    {
        if ($type === self::admin) {
            return 'admin';
        }
        if ($type === self::staff) {
            return 'staff';
        }

        throw new InvalidArgumentException('Wrong position type');
    }
}
