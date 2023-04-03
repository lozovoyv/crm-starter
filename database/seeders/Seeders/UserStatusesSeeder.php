<?php
declare(strict_types=1);

namespace Database\Seeders\Seeders;

use App\Models\Users\UserStatus;
use Database\Seeders\GenericSeeder;

class UserStatusesSeeder extends GenericSeeder
{
    protected array $data = [
        UserStatus::class => [
            UserStatus::active => ['name' => 'Активная', 'order' => 1],
            UserStatus::blocked => ['name' => 'Заблокирована', 'order' => 2],
        ],
    ];
}
