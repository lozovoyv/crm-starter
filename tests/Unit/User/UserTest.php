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

use App\Models\Users\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\Traits\CreatesUser;
use Tests\TestCase;

class UserTest extends TestCase
{
    use CreatesUser, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_base_user(): void
    {
        $user = $this->createUser('Тестовый', 'Тест', 'Тестович', 'test@test.ru', 'test', '+79876543210', 'test', '123456');

        $this->assertModelExists($user);
        $this->assertTrue($user->hasStatus(UserStatus::default));
        $this->assertTrue($user->hasStatus([UserStatus::default]));

        $this->assertEquals('Тестовый Тест Тестович', $user->fullName);
        $this->assertEquals('Тестовый Т.Т.', $user->compactName);

        $user->setStatus(UserStatus::blocked, true);
        $user->refresh();
        $this->assertTrue($user->hasStatus(UserStatus::blocked));
        $this->assertEquals(UserStatus::blocked, $user->status->id);

        $this->assertEquals('Тестовый Т.Т.', $user->historyEntryCaption());
        $this->assertNull($user->historyEntryTag());

        $user->firstname = 'Иван';
        $user->patronymic = null;
        $user->save();

        $this->assertEquals('Тестовый Иван', $user->fullName);
        $this->assertEquals('Тестовый И.', $user->compactName);
        $this->assertEquals(UserStatus::query()->find(UserStatus::blocked)->id, $user->status_id);
        $this->assertEquals('Тестовый', $user->lastname);
        $this->assertEquals('Иван', $user->firstname);
        $this->assertNull($user->patronymic);
        $this->assertEquals('test', $user->display_name);
        $this->assertEquals('test', $user->username);
        $this->assertEquals('test@test.ru', $user->email);
        $this->assertEquals('+79876543210', $user->phone);
        $this->assertTrue(Hash::check('123456', $user->password));

        $this->assertEquals(0, $user->positions->count());

        $this->assertModelMissing($user->info);

        $this->expectException(InvalidArgumentException::class);

        $user->setStatus(0);
    }
}
