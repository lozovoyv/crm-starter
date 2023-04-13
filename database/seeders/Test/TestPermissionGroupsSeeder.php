<?php
declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\Permissions\PermissionGroup;
use Database\Seeders\GenericSeeder;

class TestPermissionGroupsSeeder extends GenericSeeder
{
    public function run(): void
    {
        $i = 20;
        foreach (range(1,  $i) as $index) {
            $group = new PermissionGroup;
            $group->name = 'Группа прав №'.$index;
            $group->save();
        }
    }
}
