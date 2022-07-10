<?php

namespace Database\Seeders\Seeders;

use App\Models\Dictionaries\UserStatus;
use Database\Seeders\GenericSeeder;

class StatusesSeeder extends GenericSeeder
{
    protected array $data = [
        UserStatus::class => [
            UserStatus::active => ['name' => 'Действующий', 'lock' => true, 'order' => 1],
            UserStatus::blocked => ['name' => 'Недействующий', 'lock' => true, 'order' => 2],
        ],
    ];
}
