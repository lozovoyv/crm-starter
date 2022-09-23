<?php

namespace Database\Seeders\Test;

use App\Models\Permissions\PermissionRole;
use Database\Seeders\GenericSeeder;

class TestRolesSeeder extends GenericSeeder
{
    public function run(): void
    {
        foreach (range(1, 30) as $index) {
            $role = new PermissionRole;
            $role->name = 'Роль №'.$index;
            $role->save();
        }
    }
}
