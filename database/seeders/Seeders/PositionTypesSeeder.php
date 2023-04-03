<?php
declare(strict_types=1);

namespace Database\Seeders\Seeders;

use App\Models\Positions\PositionType;
use Database\Seeders\GenericSeeder;

class PositionTypesSeeder extends GenericSeeder
{
    protected array $data = [
        PositionType::class => [
            PositionType::admin => ['name' => 'Администратор', 'order' => 1],
            PositionType::staff => ['name' => 'Сотрудник', 'order' => 2],
        ],
    ];
}
