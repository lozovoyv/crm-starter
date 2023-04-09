<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Models\Positions\PositionStatus;

class PositionStatusDictionary extends Dictionary
{
    protected static string $dictionaryClass = PositionStatus::class;

    protected static string $name = 'Статус пользователя';

    protected static string $id_field = 'id';
    protected static string $name_field = 'name';
    protected static ?string $hint_field = null;
    protected static ?string $enabled_field = null;
    protected static ?string $order_field = 'name';
    protected static ?string $updated_at_field = 'updated_at';
    protected static ?string $locked_field = null;
}
