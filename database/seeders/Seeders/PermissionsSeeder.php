<?php
declare(strict_types=1);

namespace Database\Seeders\Seeders;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    protected array $permissions = [];

    protected array $scopes = [];

    protected function loadDefinitions(): void
    {
        $this->scopes = Permission::$scopes;

        foreach (Permission::$permissions as $name => $permission) {
            $this->add($name, $permission['name'], $permission['description'] ?? null);
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
        $scopes = explode('__', $key, 2)[0];
        if (!isset($this->permissions[$scopes])) {
            $this->permissions[$scopes] = [];
        }
        $this->permissions[$scopes][$key] = [
            'key' => $key,
            'scopes' => $scopes,
            'name' => $name,
            'description' => $description,
            'order' => count($this->permissions[$scopes]) + 1,
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

        foreach ($this->scopes as $scopeKey => $scopeName) {
            /** @var PermissionScope|null $module */
            $scope = PermissionScope::query()->where('scope_name', $scopeKey)->first();
            if ($scope === null) {
                $scope = new PermissionScope();
            }
            $scope->name = $scopeName;
            $scope->scope_name = $scopeKey;
            $scope->order = array_flip(array_keys($this->scopes))[$scopeKey];
            $scope->save();
        }

        $permissionModules = $this->permissions();

        if (empty($permissionModules)) {
            Permission::query()->delete();

        } else {
            $processed = [];

            foreach ($permissionModules as $scope => $permissions) {
                if (empty($permissions)) {
                    Permission::query()->where('scope_name', $scope)->delete();
                } else {
                    foreach ($permissions as $key => $data) {
                        $permission = Permission::query()->where('key', $key)->first();
                        if ($permission === null) {
                            $permission = new Permission();
                            $permission->key = $key;
                        }
                        $permission->scope_name = $scope;
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
