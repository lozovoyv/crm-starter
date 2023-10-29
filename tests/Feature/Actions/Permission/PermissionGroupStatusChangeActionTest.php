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

use App\Actions\Permission\PermissionGroupStatusChangeAction;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Models\History\History;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\PositionType;
use App\VDTO\PermissionGroupVDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesCurrent;

class PermissionGroupStatusChangeActionTest extends TestCase
{
    use CreatesCurrent, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_execute(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupStatusChangeAction($current);
        $vdto = new PermissionGroupVDTO(['active' => true]);

        $permissionGroup = new PermissionGroup([
            'name' => 'PermissionGroupStatusChangeActionTest',
            'description' => 'Description',
        ]);

        $permissionGroup->save();

        // Assert activated
        $action->execute($permissionGroup, $vdto);
        $this->assertTrue($permissionGroup->active);

        // Assert history
        $this->assertEquals(1, $permissionGroup->history->count());
        /** @var History|null $history */
        $history = $permissionGroup->history->first();
        $this->assertNotNull($history);
        $this->assertEquals(HistoryAction::permission_group_activated, $history->action_id);
        $this->assertEquals($current->positionId(), $history->position_id);

        // Assert deactivated
        $vdto = new PermissionGroupVDTO(['active' => false]);
        $action->execute($permissionGroup, $vdto);
        $this->assertFalse($permissionGroup->active);

        $permissionGroup->refresh();

        // Assert history
        $this->assertEquals(2, $permissionGroup->history->count());
        /** @var History|null $history */
        $history = $permissionGroup->history()->latest()->first();
        $this->assertNotNull($history);
        $this->assertEquals(HistoryAction::permission_group_deactivated, $history->action_id);
        $this->assertEquals($current->positionId(), $history->position_id);
    }

    public function test_execute_not_existing(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupStatusChangeAction($current);
        $vdto = new PermissionGroupVDTO(['active' => true]);

        $permissionGroup = new PermissionGroup(['name' => 'PermissionGroupStatusChangeActionTest 2', 'description' => 'Description']);

        // Assert not existing
        $this->expectException(ModelNotFoundException::class);
        $action->execute($permissionGroup, $vdto);
    }

    public function test_execute_locked(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupStatusChangeAction($current);
        $vdto = new PermissionGroupVDTO(['active' => true]);

        $permissionGroup = new PermissionGroup(['name' => 'PermissionGroupStatusChangeActionTest 3', 'description' => 'Description', 'locked' => true]);
        $permissionGroup->save();

        // Assert not existing
        $this->expectException(ModelLockedException::class);
        $action->execute($permissionGroup, $vdto);
    }
}
