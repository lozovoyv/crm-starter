<?php

namespace Database\Seeders;

use Database\Seeders\Seeders\HistoryActionsSeeder;
use Database\Seeders\Seeders\PermissionsSeeder;
use Database\Seeders\Seeders\PermissionRolesSeeder;
use Database\Seeders\Seeders\PositionStatusesSeeder;
use Database\Seeders\Seeders\PositionTypesSeeder;
use Database\Seeders\Seeders\UserStatusesSeeder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected array $seeders = [
        PermissionsSeeder::class,
        PermissionRolesSeeder::class,
        HistoryActionsSeeder::class,
        UserStatusesSeeder::class,
        PositionStatusesSeeder::class,
        PositionTypesSeeder::class,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    public function run(): void
    {
        foreach ($this->seeders as $seederClass) {

            /** @var GenericSeeder|Seeder $seeder */
            $seeder = $this->container->make($seederClass);

            $seeder->run();
        }
    }
}
