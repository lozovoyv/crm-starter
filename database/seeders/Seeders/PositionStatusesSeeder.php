<?php

namespace Database\Seeders\Seeders;

use App\Models\Positions\PositionStatus;
use Database\Seeders\GenericSeeder;

class PositionStatusesSeeder extends GenericSeeder
{
    protected array $data = [
        PositionStatus::class => [
            PositionStatus::active => ['name' => 'Активный', 'order' => 1],
            PositionStatus::blocked => ['name' => 'Заблокирован', 'order' => 2],
        ],
    ];
}
