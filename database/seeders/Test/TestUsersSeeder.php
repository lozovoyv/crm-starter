<?php
declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\Users\User;
use Database\Seeders\GenericSeeder;

class TestUsersSeeder extends GenericSeeder
{
    public function run(): void
    {
        User::factory(100)->create();
    }
}
