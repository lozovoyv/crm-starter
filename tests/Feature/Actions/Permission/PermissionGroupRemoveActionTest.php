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

use App\Actions\Permission\PermissionGroupRemoveAction;
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Models\History\History;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\PositionType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Tests\Traits\CreatesCurrent;

class PermissionGroupRemoveActionTest extends TestCase
{
    use CreatesCurrent, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_execute(): void
    {
        $current = $this->initCurrent(PositionType::admin);

        $permissionGroup = new PermissionGroup([
            'name' => 'PermissionGroupRemoveActionTest',
            'description' => 'Description',
        ]);
        $permissionGroup->save();
        $id = $permissionGroup->id;

        $action = new PermissionGroupRemoveAction($current);
        $action->execute($permissionGroup);

        // Assert deleted
        $this->assertModelMissing($permissionGroup);

        // Assert history
        $history = History::query()->whereEntryType(PermissionGroup::class)->whereEntryID($id)->first();
        $this->assertNotNull($history);
        $this->assertEquals(HistoryAction::permission_group_deleted, $history->action_id);
        $this->assertEquals($current->positionId(), $history->position_id);
    }

    public function test_execute_not_existing(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupRemoveAction($current);
        $permissionGroup = new PermissionGroup(['name' => 'PermissionGroupRemoveActionTest 2', 'description' => 'Description']);

        $this->expectException(ModelNotFoundException::class);
        $action->execute($permissionGroup);
    }

    public function test_execute_locked(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupRemoveAction($current);
        $permissionGroup = new PermissionGroup(['name' => 'PermissionGroupRemoveActionTest 3', 'description' => 'Description', 'locked' => true]);
        $permissionGroup->save();

        $this->expectException(ModelLockedException::class);
        $action->execute($permissionGroup);
    }

    public function test_execute_blocked(): void
    {
        $current = $this->initCurrent(PositionType::admin);
        $action = new PermissionGroupRemoveAction($current);
        $permissionGroup = new PermissionGroup(['name' => 'Test', 'description' => 'Description']);
        $permissionGroup->save();

        if (!Schema::hasTable('test_permission_group_block')) {
            Schema::create('test_permission_group_block', static function (Blueprint $table) {
                $table->unsignedSmallInteger('id', true);
                $table->unsignedSmallInteger('group_id')->nullable();
                $table->foreign('group_id')->references('id')->on('permission_groups')->restrictOnDelete();
            });
        } else {
            DB::table('test_permission_group_block')->truncate();
        }
        DB::table('test_permission_group_block')->insert(['id' => 1, 'group_id' => $permissionGroup->id]);

        $this->expectException(ModelDeleteBlockedException::class);
        $action->execute($permissionGroup);
    }
}
