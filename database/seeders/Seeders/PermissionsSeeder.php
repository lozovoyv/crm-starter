<?php

namespace Database\Seeders\Seeders;

use App\Models\Permissions\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected array $permissions = [
        'system' => [
            //'system.settings' => ['name' => 'Просмотр и редактирование настроек системы', 'order' => 1],
        ],
        'dictionaries' => [
            //'dictionaries.edit' => ['name' => 'Редактирование справочников', 'order' => 0],
        ],
        'roles' => [
            //'roles.edit' => ['name' => 'Редактирование ролей', 'order' => 0],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (empty($this->permissions)) {
            Permission::query()->delete();

        } else {
            $processed = [];

            foreach ($this->permissions as $module => $permissions) {
                if (empty($permissions)) {
                    Permission::query()->where('module', $module)->delete();
                } else {
                    foreach ($permissions as $key => $data) {
                        $permission = Permission::query()->where('key', $key)->first();
                        if ($permission === null) {
                            $permission = new Permission();
                            $permission->key = $key;
                        }
                        $permission->module = $module;
                        $permission->name = $data['name'];
                        $permission->order = $data['order'];
                        $permission->save();
                        $processed[] = $permission->id;
                    }
                }
            }

            Permission::query()->whereNotIn('id', $processed)->delete();
        }
    }
}
