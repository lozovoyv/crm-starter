<?php

namespace Database\Seeders;

use Database\Seeders\Test\TestRolesSeeder;
use Database\Seeders\Test\TestUsersSeeder;
use Exception;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    protected array $seeders = [
        TestRolesSeeder::class,
        TestUsersSeeder::class,
    ];

    /**
     * Run the database seeds.
     *
     * @return  void
     *
     * @throws Exception
     */
    public function run(): void
    {
        foreach ($this->seeders as $seederClass) {
            /** @var GenericSeeder $seeder */
            $seeder = $this->container->make($seederClass);
            $seeder->run();
        }
    }
}
