<?php

namespace Database\Seeders\Seeders;

use App\Models\Users\UserStatus;
use Database\Seeders\GenericSeeder;

class UserStatusesSeeder extends GenericSeeder
{
    protected array $data = [
        UserStatus::class => [
            UserStatus::active => ['name' => 'Активный', 'lock' => true, 'order' => 1],
            UserStatus::blocked => ['name' => 'Заблокирован', 'lock' => true, 'order' => 2],
        ],
    ];
}
