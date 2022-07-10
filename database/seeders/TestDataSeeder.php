<?php

namespace Database\Seeders;

use App\Models\User\User;
use App\Models\User\UserInfo;
use Exception;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    protected array $seeders = [
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

        // Create users with profiles
        User::factory(100)
            ->afterCreating(function (User $user) {
                UserInfo::factory()->create(['user_id' => $user->id]);
            })
            ->create();
    }
}
