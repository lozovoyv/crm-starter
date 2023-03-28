<?php
declare(strict_types=1);

namespace Tests\Feature\Permissions;

use App\Models\EntryScope;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionModule;
use App\Models\Permissions\PermissionRole;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_permission(): void
    {
        // Module 1
        $module = new PermissionModule();
        $module->module = 'test';
        $module->name = 'test module';
        $module->save();
        $this->assertModelExists($module);

        // Permission 1
        /** @var Permission $permission */
        $permission = $module->permissions()->create();
        $permission->key = 'test.permission';
        $permission->name = 'test permission';
        $permission->description = 'test permission description';
        $permission->order = 1;
        $permission->save();
        $this->assertModelExists($permission);

        $this->assertEquals([
            'id' => 1,
            'key' => 'test.permission',
            'module' => 'test module',
            'name' => 'test permission',
            'description' => 'test permission description',
        ], $permission->toArray());

        // Module 2
        $module2 = new PermissionModule();
        $module2->module = 'test 2';
        $module2->name = 'test module 2';
        $module2->save();
        $this->assertModelExists($module2);

        // Permission 2
        /** @var Permission $permission2 */
        $permission2 = $module2->permissions()->create();
        $permission2->key = 'test.permission2';
        $permission2->name = 'test permission 2';
        $permission2->description = 'test permission 2 description';
        $permission2->order = 1;
        $permission2->save();
        $this->assertModelExists($permission2);

        // Check bindings
        $module->load('permissions');
        $this->assertEquals(1, $module->permissions->count());
        $this->assertTrue($permission->permissionModule->is($module));

        $module2->load('permissions');
        $this->assertEquals(1, $module2->permissions->count());
        $this->assertTrue($permission2->permissionModule->is($module2));

        // Role
        $now = Carbon::now()->milliseconds(0);
        $role = new PermissionRole();
        $role->name = 'test role';
        $role->updated_at = $now;
        $role->save();

        $this->assertModelExists($role);
        $this->assertTrue($role->active);
        $this->assertFalse($role->locked);

        $role->permissions()->sync([$permission->id]);
        $role->load('permissions');

        $this->assertEquals(1, $role->permissions->count());

        $this->assertEquals(1, $permission->roles->count());
        $this->assertEquals(0, $permission2->roles->count());

        $this->assertEquals('test role', $role->historyEntryTitle());
        $this->assertEquals(EntryScope::role, $role->historyEntryName());
        $this->assertEquals(null, $role->historyEntryType());
        $this->assertIsString($role->hash());
        $this->assertTrue($role->matches(100));
        $this->assertFalse($role->matches(101));
        $this->assertEquals([
            'id' => 100,
            'name' => 'test role',
            'count' => 1,
            'description' => null,
            'active' => true,
            'locked' => false,
            'hash' => $role->getHash(),
            'updated_at' => $now,
        ], $role->toArray());
        $this->assertEquals(md5($now->toString()), $role->getHash());
    }

    public function test_admin_role(): void
    {
        $this->seed();

        /** @var PermissionRole|null $role */
        $role = PermissionRole::query()->where('id', PermissionRole::super)->first();

        $this->assertModelExists($role);
        $this->assertTrue($role->matches(PermissionRole::super));
        $this->assertTrue($role->matches('super'));
        $this->assertEquals([
            'id' => PermissionRole::super,
            'name' => $role->name,
            'count' => Permission::query()->count(),
            'description' => $role->description,
            'active' => true,
            'locked' => true,
            'hash' => $role->getHash(),
            'updated_at' => $role->updated_at,
        ], $role->toArray());
    }
}
