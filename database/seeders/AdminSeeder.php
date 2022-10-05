<?php

namespace Database\Seeders;

use App\Models\Permissions\PermissionRole;
use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
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

        if ($admin === null) {
            $admin = new User();
            $admin->username = 'admin';
            $admin->password = Hash::make('admin');
            $admin->save();
            $admin->info()->create(['lastname' => 'Администратор', 'gender' => 'male', 'email' => 'admin@admin.admin']);
            /** @var Position $position */
            $position = $admin->positions()->create([
                'status_id' => PositionStatus::active,
                'type_id' => PositionType::staff,
            ]);
            $position->roles()->attach(PermissionRole::super);
        }
    }
}
