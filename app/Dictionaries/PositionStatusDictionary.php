<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;

class PositionStatusDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = PositionStatus::class;

    protected static string $title = 'Статус пользователя';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.staff', 'system.staff.change']];

    protected static ?string $enabled_field = null;
    protected static ?string $order_field = 'name';
    protected static ?string $locked_field = null;
}
