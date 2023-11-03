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

namespace Tests\Unit;

use App\Current;
use App\Exceptions\NoPositionSelectedException;
use App\Exceptions\PositionMismatchException;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Tests\TestCase;
use Tests\Traits\CreatesPosition;
use Tests\Traits\CreatesUser;

class CurrentTest extends TestCase
{
    use CreatesUser, CreatesPosition, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_current_no_user(): void
    {
        $request = Request::create('/');
        $current = Current::init($request, true);
        $this->assertFalse($current->isAuthenticated());
        $this->assertNull($current->user());
        $this->assertNull($current->position());
    }

    public function test_current_user_no_position(): void
    {
        $user = $this->createUser();
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $current = Current::init($request, true);
        $this->assertTrue($current->isAuthenticated());
        $this->assertNotNull($current->user());
        $this->assertTrue($current->user()->is($user));
        $this->assertNull($current->position());
    }

    public function test_current_user_details(): void
    {
        $user = $this->createUser('Тестовый', 'Тест', 'Тестович', 'test@test.com', 'tester');
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $current = Current::init($request, true);
        $this->assertEquals($user->id, $current->userId());
        $this->assertEquals('Тестовый Т.Т.', $current->compactName());
        $this->assertEquals('Тестовый Тест Тестович', $current->fullName());
        $this->assertEquals('test@test.com', $current->email());
    }

    public function test_current_user_one_position(): void
    {
        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $current = Current::init($request, true);
        $this->assertTrue($current->isAuthenticated());
        $this->assertNotNull($current->user());
        $this->assertTrue($current->user()->is($user));
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position));
        $this->assertEquals($position->id, $current->positionId());
    }

    public function test_current_user_one_position_preselected(): void
    {
        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->session()->put('position_id', $position->id);
        $current = Current::init($request, true);
        $this->assertTrue($current->isAuthenticated());
        $this->assertNotNull($current->user());
        $this->assertTrue($current->user()->is($user));
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position));
        $this->assertEquals($position->id, $current->positionId());
    }

    public function test_current_user_several_positions(): void
    {
        $user = $this->createUser();
        $this->createPosition($user, PositionType::staff);
        $this->createPosition($user, PositionType::admin);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $this->expectException(NoPositionSelectedException::class);
        Current::init($request, true);
    }

    public function test_current_user_several_positions_preselected(): void
    {
        $user = $this->createUser();
        $this->createPosition($user, PositionType::staff);
        $position2 = $this->createPosition($user, PositionType::admin);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->session()->put('position_id', $position2->id);
        $current = Current::init($request, true);
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position2));
        $this->assertEquals($position2->id, $current->positionId());
    }

    public function test_current_user_several_positions_preselected_mismatch(): void
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $this->createPosition($user, PositionType::staff);
        $position2 = $this->createPosition($user2, PositionType::admin);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->session()->put('position_id', $position2->id);
        $this->expectException(PositionMismatchException::class);
        Current::init($request, true);
    }

    public function test_current_user_proxy_by_admin(): void
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $position = $this->createPosition($user, PositionType::admin);
        $position2 = $this->createPosition($user2, PositionType::staff);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->cookies->set('proxy_position', $position2->id);
        $current = Current::init($request, true);
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position2));
        $this->assertEquals($position2->id, $current->positionId());
    }

    public function test_current_user_proxy_by_no_permission(): void
    {
        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $user2 = $this->createUser();
        $position2 = $this->createPosition($user2, PositionType::admin);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->cookies->set('proxy_position', $position2->id);
        $current = Current::init($request, true);
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position));
        $this->assertEquals($position->id, $current->positionId());
    }

    public function test_current_user_proxy_by_permission(): void
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $position->permissions()->sync([Permission::get(Permission::system__act_as_other)?->id]);
        $position2 = $this->createPosition($user2, PositionType::admin);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->cookies->set('proxy_position', $position2->id);
        $current = Current::init($request, true);
        $this->assertNotNull($current->position());
        $this->assertTrue($current->position()->is($position2));
        $this->assertEquals($position2->id, $current->positionId());
    }

    public function test_current_permissions(): void
    {
        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $position->permissions()->sync([
            Permission::get(Permission::system__act_as_other)?->id,
            Permission::get(Permission::system__history)?->id,
        ]);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $current = Current::init($request, true);
        $permissions = array_values($current->permissions());
        sort($permissions);
        $this->assertEquals([Permission::system__act_as_other, Permission::system__history], $permissions);
    }

    public function test_current_proxy_permissions(): void
    {
        $user = $this->createUser();
        $position = $this->createPosition($user, PositionType::staff);
        $position->permissions()->sync([Permission::get(Permission::system__act_as_other)?->id]);

        $user2 = $this->createUser();
        $position2 = $this->createPosition($user2, PositionType::staff);
        $position2->permissions()->sync([Permission::get(Permission::system__history)?->id]);

        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->cookies->set('proxy_position', $position2->id);
        $current = Current::init($request, true);

        $this->assertEquals([Permission::system__history], array_values($current->permissions()));
    }

    public function test_current_has_position_type(): void
    {
        $user = $this->createUser();
        $this->createPosition($user, PositionType::staff);
        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $current = Current::init($request, true);

        $this->assertTrue($current->hasPositionType(PositionType::staff));
        $this->assertFalse($current->hasPositionType(PositionType::admin));
    }

    public function test_current_has_proxy_position_type(): void
    {
        $user = $this->createUser();
        $this->createPosition($user, PositionType::admin);

        $user2 = $this->createUser();
        $position2 = $this->createPosition($user2, PositionType::staff);

        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->cookies->set('proxy_position', $position2->id);
        $current = Current::init($request, true);

        $this->assertTrue($current->hasPositionType(PositionType::staff));
        $this->assertFalse($current->hasPositionType(PositionType::admin));
    }
}
