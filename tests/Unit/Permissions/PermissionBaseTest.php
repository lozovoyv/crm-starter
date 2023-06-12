<?php
declare(strict_types=1);

namespace Tests\Unit\Permissions;

use App\Models\EntryScope;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use App\Models\Permissions\PermissionGroup;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_permission(): void
    {
        // Module 1
        $scope = new PermissionScope();
        $scope->scope_name = 'test';
        $scope->name = 'test scope';
        $scope->save();
        $this->assertModelExists($scope);

        // Permission 1
        /** @var Permission $permission */
        $permission = $scope->permissions()->create();
        $permission->key = 'test.permission';
        $permission->name = 'test permission';
        $permission->description = 'test permission description';
        $permission->order = 1;
        $permission->save();
        $this->assertModelExists($permission);

        $this->assertTrue(Permission::get('test.permission')->is($permission));
        $this->assertNull(Permission::get('test.permission.not.existing'));

        $this->assertEquals('test.permission', $permission->key);
        $this->assertEquals('test', $permission->scope_name);
        $this->assertEquals('test permission', $permission->name);
        $this->assertEquals('test permission description', $permission->description);

        // Module 2
        $scope2 = new PermissionScope();
        $scope2->scope_name = 'test 2';
        $scope2->name = 'test scope 2';
        $scope2->save();
        $this->assertModelExists($scope2);

        // Permission 2
        /** @var Permission $permission2 */
        $permission2 = $scope2->permissions()->create();
        $permission2->key = 'test.permission2';
        $permission2->name = 'test permission 2';
        $permission2->description = 'test permission 2 description';
        $permission2->order = 1;
        $permission2->save();
        $this->assertModelExists($permission2);

        // Check bindings
        $scope->load('permissions');
        $this->assertEquals(1, $scope->permissions->count());
        $this->assertTrue($permission->scope->is($scope));

        $scope2->load('permissions');
        $this->assertEquals(1, $scope2->permissions->count());
        $this->assertTrue($permission2->scope->is($scope2));

        // Role
        $now = Carbon::now()->milliseconds(0);
        $group = new PermissionGroup();
        $group->name = 'test role';
        $group->updated_at = $now;
        $group->save();

        $this->assertModelExists($group);
        $this->assertTrue($group->active);
        $this->assertFalse($group->locked);

        $group->permissions()->sync([$permission->id]);
        $group->load('permissions');

        $this->assertEquals(1, $group->permissions->count());

        $this->assertEquals(1, $permission->groups->count());
        $this->assertEquals(0, $permission2->groups->count());

        $this->assertEquals('test role', $group->historyEntryTitle());
        $this->assertEquals(EntryScope::permission_group, $group->historyEntryName());
        $this->assertEquals(null, $group->historyEntryType());
        $this->assertIsString($group->hash());
        $this->assertEquals('test role', $group->name);
        $this->assertEquals(1, $group->permissions->count());
        $this->assertEquals(null, $group->description);
        $this->assertEquals(true, $group->active);
        $this->assertEquals(false, $group->locked);
        $this->assertEquals(md5($now->toString()), $group->getHash());
    }
}
