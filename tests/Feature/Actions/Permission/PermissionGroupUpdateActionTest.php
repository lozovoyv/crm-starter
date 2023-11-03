<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Feature\Actions\Permission;

use App\Actions\Permission\PermissionGroupUpdateAction;
use App\Exceptions\Model\ModelLockedException;
use App\Models\History\History;
use App\Models\History\HistoryAction;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Permissions\PermissionScope;
use App\Models\Positions\PositionType;
use App\VDTO\PermissionGroupVDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesCurrent;

class PermissionGroupUpdateActionTest extends TestCase
{
    use CreatesCurrent, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    /**
     * @throws ModelLockedException
     */
    public function test_execute(): void
    {
        $current = $this->initCurrent(PositionType::admin);

        $action = new PermissionGroupUpdateAction($current);

        $permissionGroup = new PermissionGroup();

        // Create new permission group
        $vdto = new PermissionGroupVDTO([
            'name' => 'PermissionGroupUpdateActionTest',
            'active' => true,
            'description' => 'Description',
        ]);
        $action->execute($permissionGroup, $vdto);

        // Assert permission group
        $this->assertTrue($permissionGroup->wasRecentlyCreated);
        $this->assertEquals('PermissionGroupUpdateActionTest', $permissionGroup->name);
        $this->assertTrue($permissionGroup->active);
        $this->assertEquals('Description', $permissionGroup->description);
        $this->assertEmpty($permissionGroup->permissions);

        // Assert history
        $this->assertEquals(1, $permissionGroup->history->count());
        /** @var History|null $history */
        $history = $permissionGroup->history->first();
        $this->assertNotNull($history);
        $this->assertEquals(HistoryAction::permission_group_created, $history->action_id);
        $this->assertEquals(2, $history->changes->count());
        $this->assertEquals([
            ['parameter' => 'name', 'old' => null, 'new' => 'PermissionGroupUpdateActionTest'],
            ['parameter' => 'description', 'old' => null, 'new' => 'Description'],
        ], $history->changes->toArray());
        $this->assertEquals($current->positionId(), $history->position_id);

        // Create test permission
        $scope = new PermissionScope(['scope_name' => 'test', 'name' => 'test']);
        $scope->save();
        $permission = new Permission(['key' => 'test__test', 'scope_name' => 'test', 'name' => 'Test permission']);
        $permission->save();

        $permissionGroup = PermissionGroup::query()->where('id', $permissionGroup->id)->first();

        $this->assertNotNull($permissionGroup);

        // Update existing permission group
        $vdto = new PermissionGroupVDTO([
            'name' => 'PermissionGroupUpdateActionTest',
            'active' => true,
            'description' => 'Description',
            'permissions' => [
                $permission->id,
            ],
        ]);
        $action->execute($permissionGroup, $vdto);

        $permissionGroup->refresh();

        // Assert permission group
        $this->assertFalse($permissionGroup->wasRecentlyCreated);
        $this->assertEquals('PermissionGroupUpdateActionTest', $permissionGroup->name);
        $this->assertTrue($permissionGroup->active);
        $this->assertEquals('Description', $permissionGroup->description);
        $this->assertEquals([$permission->id], $permissionGroup->permissions->pluck('id')->toArray());

        // Assert history
        $this->assertEquals(2, $permissionGroup->history->count());
        /** @var History|null $history */
        $history = $permissionGroup->history()->latest()->first();
        $this->assertNotNull($history);
        $this->assertEquals(HistoryAction::permission_group_edited, $history->action_id);
        $this->assertEquals(1, $history->changes->count());
        $this->assertEquals([
            ['parameter' => 'permissions', 'old' => null, 'new' => [$permission->id]],
        ], $history->changes->toArray());
        $this->assertEquals($current->positionId(), $history->position_id);
    }

    public function test_execute_locked(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $permissionGroup = new PermissionGroup(['name' => 'PermissionGroupUpdateActionTest 2', 'description' => 'Description', 'locked' => true]);
        $permissionGroup->save();


        $action = new PermissionGroupUpdateAction($current);

        $this->expectException(ModelLockedException::class);

        $vdto = new PermissionGroupVDTO([
            'name' => 'PermissionGroupUpdateActionTest 3',
            'active' => true,
            'description' => 'Description',
        ]);
        $action->execute($permissionGroup, $vdto);
    }
}
