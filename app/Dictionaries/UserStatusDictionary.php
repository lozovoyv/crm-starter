<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Models\Users\UserStatus;

class UserStatusDictionary extends Dictionary
{
    protected static string $dictionaryClass = UserStatus::class;

    protected static string $name = 'Статус учётной записи';

    protected static ?string $order_field = 'name';
    protected static ?string $locked_field = null;
    protected static ?string $enabled_field = null;
}
