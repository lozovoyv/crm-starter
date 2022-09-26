<?php

namespace Database\Seeders\Test;

use App\Models\Permissions\PermissionRole;
use Database\Seeders\GenericSeeder;

class TestRolesSeeder extends GenericSeeder
{
    public function run(): void
    {
        $i = 90;
        foreach (range(101 + $i, 110 + $i) as $index) {
            $role = new PermissionRole;
            $role->name = 'Роль №'.$index;
            $role->save();
        }
    }
}
