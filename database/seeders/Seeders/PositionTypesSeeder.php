<?php

namespace Database\Seeders\Seeders;

use App\Models\Positions\PositionType;
use Database\Seeders\GenericSeeder;

class PositionTypesSeeder extends GenericSeeder
{
    protected array $data = [
        PositionType::class => [
            PositionType::staff => ['name' => 'Сотрудник', 'order' => 1],
        ],
    ];
}
