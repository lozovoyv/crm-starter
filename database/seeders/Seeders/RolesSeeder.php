<?php

namespace Database\Seeders\Seeders;

use App\Models\Permissions\PermissionRole;
use Database\Seeders\GenericSeeder;

class RolesSeeder extends GenericSeeder
{
    protected array $data = [
        PermissionRole::class => [
            PermissionRole::super => ['name' => 'Администратор', 'locked' => true, 'description' => 'Роль администратора системы.'],
        ],
    ];
}
