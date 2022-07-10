<?php

namespace Database\Seeders\Seeders;

use App\Models\Permissions\Role;
use Database\Seeders\GenericSeeder;

class RolesSeeder extends GenericSeeder
{
    protected array $data = [
        Role::class => [
            Role::super => ['name' => 'Администратор', 'locked' => true, 'description' => 'Роль администратора системы.'],
        ],
    ];
}
