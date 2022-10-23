<?php

namespace Database\Seeders\Seeders;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionModule;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected array $permissions = [];

    protected array $modules = [];

    protected function loadDefinitions(): void
    {
        $data = require base_path('/app/permissions.php');
        $this->modules = $data['modules'];
        $permissions = $data['permissions'];

        foreach ($permissions as $permission) {
            $this->add($permission[0], $permission[1], $permission[2] ?? null);
        }
    }

    protected function permissions(): array
    {
        return $this->permissions;
    }

    /**
     * Make permission record.
     *
     * @param string $key
     * @param string $name
     * @param string $description
     *
     * @return  void
     */
    protected function add(string $key, string $name, string $description): void
    {
        $module = explode('.', $key, 2)[0];
        if (!isset($this->permissions[$module])) {
            $this->permissions[$module] = [];
        }
        $this->permissions[$module][$key] = [
            'key' => $key,
            'module' => $module,
            'name' => $name,
            'description' => $description,
            'order' => count($this->permissions[$module]) + 1,
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->loadDefinitions();

        foreach ($this->modules as $moduleKey => $moduleName) {
            /** @var PermissionModule|null $module */
            $module = PermissionModule::query()->where('module', $moduleKey)->first();
            if ($module === null) {
                $module = new PermissionModule();
            }
            $module->name = $moduleName;
            $module->module = $moduleKey;
            $module->order = array_flip(array_keys($this->modules))[$moduleKey];
            $module->save();
        }

        $permissionModules = $this->permissions();

        if (empty($permissionModules)) {
            Permission::query()->delete();

        } else {
            $processed = [];

            foreach ($permissionModules as $module => $permissions) {
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
                        $permission->description = $data['description'];
                        $permission->save();
                        $processed[] = $permission->id;
                    }
                }
            }

            Permission::query()->whereNotIn('id', $processed)->delete();
        }
    }
}
