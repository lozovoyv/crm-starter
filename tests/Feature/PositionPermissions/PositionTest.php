<?php
declare(strict_types=1);

namespace Tests\Feature\PositionPermissions;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Permissions\PermissionScope;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HelperTraits\CreatesPosition;
use Tests\HelperTraits\CreatesUser;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use CreatesUser, CreatesPosition, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_position_permissions(): void
    {
        $scope = new PermissionScope();
        $scope->scope_name = 'test';
        $scope->name = 'test scope';
        $scope->save();
        $this->assertModelExists($scope);

        /** @var Permission $permission */
        $permission = $scope->permissions()->create();
        $permission->key = 'test.permission';
        $permission->name = 'test permission';
        $permission->save();
        $this->assertModelExists($permission);

        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);

        $position->permissions()->sync([$permission->id]);

        $this->assertEquals(['test.permission'], array_values($position->getPermissionsList()));
        $this->assertTrue($position->can('test.permission'));
        $this->assertTrue($position->can(''));
        $this->assertTrue($position->can(null));
        $this->assertFalse($position->can('test.permission.2'));
    }

    public function test_position_permission_group(): void
    {
        $scope = new PermissionScope();
        $scope->scope_name = 'test';
        $scope->name = 'test scope';
        $scope->save();
        $this->assertModelExists($scope);

        /** @var Permission $permission */
        $permission = $scope->permissions()->create();
        $permission->key = 'test.permission';
        $permission->name = 'test permission';
        $permission->save();
        $this->assertModelExists($permission);

        $group = new PermissionGroup();
        $group->save();
        $group->permissions()->sync([$permission->id]);

        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);

        $position->permissionGroups()->sync([$group->id]);

        $this->assertEquals(['test.permission'], array_values($position->getPermissionsList()));
        $this->assertTrue($position->can('test.permission'));
        $this->assertTrue($position->can(''));
        $this->assertTrue($position->can(null));
        $this->assertFalse($position->can('test.permission.2'));
    }

    public function test_position_admin(): void
    {
        $scope = new PermissionScope();
        $scope->scope_name = 'test';
        $scope->name = 'test scope';
        $scope->save();
        $this->assertModelExists($scope);

        /** @var Permission $permission */
        $permission = $scope->permissions()->create();
        $permission->key = 'test.permission';
        $permission->name = 'test permission';
        $permission->save();
        $this->assertModelExists($permission);

        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::admin);

        $this->assertContains('test.permission', $position->getPermissionsList());
        $this->assertTrue($position->can('test.permission'));
        $this->assertTrue($position->can(''));
        $this->assertTrue($position->can(null));
        $this->assertFalse($position->can('test.permission.2'));
    }
}
