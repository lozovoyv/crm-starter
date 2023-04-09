<?php
declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\Permissions\PermissionGroup;
use Database\Seeders\GenericSeeder;

class TestRolesSeeder extends GenericSeeder
{
    public function run(): void
    {
        $i = 20;
        foreach (range(1,  $i) as $index) {
            $role = new PermissionGroup;
            $role->name = 'Роль №'.$index;
            $role->save();
        }
    }
}
