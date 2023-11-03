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

namespace Tests\Unit\User;

use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreatesUser;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use CreatesUser, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_base_position(): void
    {
        $user = $this->createUser('base', 'position', 'test');
        $this->assertModelExists($user);
        $this->assertEquals(0, $user->positions->count());

        $position = new Position();
        $position->user_id = $user->id;
        $position->type_id = PositionType::staff;
        $position->save();
        $user->refresh();
        $this->assertModelExists($position);

        $this->assertTrue($position->hasType(PositionType::staff));
        $this->assertTrue($position->hasType('staff'));
        $this->assertFalse($position->hasType(PositionType::admin));
        $this->assertFalse($position->hasType('admin'));

        $this->assertTrue($position->user->is($user));
        $this->assertEquals(1, $user->positions->count());
        $this->assertTrue($position->is($user->positions->first()));
        $this->assertEquals(PositionStatus::default, $position->status->id);
        $this->assertEquals(PositionType::staff, $position->type->id);

        $position->setStatus(PositionStatus::blocked, true);
        $position->refresh();
        $this->assertEquals(PositionStatus::blocked, $position->status->id);

        $this->assertEquals('Base P.T.', $position->historyEntryCaption());
        $this->assertEquals('staff', $position->historyEntryTag());
        $this->assertEquals($user->id, $position->user_id);
    }

    public function test_base_position_no_user(): void
    {
        $position = new Position();
        $position->type_id = PositionType::staff;
        $this->expectException(QueryException::class);
        $position->save();
    }

    public function test_base_position_no_type(): void
    {
        $user = $this->createUser('base', 'position', 'test');
        $this->assertModelExists($user);

        $position = new Position();
        $position->user_id = $user->id;
        $this->expectException(QueryException::class);
        $position->save();
    }
}
