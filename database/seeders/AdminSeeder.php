<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create super admin user
        /** @var User $admin */
        $admin = User::query()->where('id', 1)->first();

        if($admin === null) {
            $admin = new User;
            $admin->username = 'admin';
            $admin->password = Hash::make('admin');
            $admin->save();
            $admin->info()->create(['lastname' => 'Администратор', 'firstname' => 'Администратор', 'gender' => 'male', 'email' => 'admin@admin.admin']);
            // $admin->roles()->attach(Role::super);
        }
    }
}
