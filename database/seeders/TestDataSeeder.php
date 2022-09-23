<?php

namespace Database\Seeders;

use Database\Seeders\Test\TestRolesSeeder;
use Exception;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    protected array $seeders = [
        TestRolesSeeder::class,
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
