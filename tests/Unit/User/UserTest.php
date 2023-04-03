<?php
declare(strict_types=1);

namespace Tests\Unit\User;

use App\Models\EntryScope;
use App\Models\Users\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HelperTraits\CreatesUser;
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

        $this->assertEquals('Тестовый Т.Т.', $user->historyEntryTitle());
        $this->assertEquals(EntryScope::user, $user->historyEntryName());
        $this->assertEquals(null, $user->historyEntryType());

        $user->firstname = 'Иван';
        $user->patronymic = null;
        $user->save();

        $this->assertEquals('Тестовый Иван', $user->fullName);
        $this->assertEquals('Тестовый И.', $user->compactName);
        $this->assertEquals([
            'id' => $user->id,
            'is_active' => false,
            'status' => UserStatus::get(UserStatus::blocked)->name,
            'lastname' => 'Тестовый',
            'firstname' => 'Иван',
            'patronymic' => null,
            'display_name' => 'test',
            'username' => 'test',
            'email' => 'test@test.ru',
            'has_password' => true,
            'phone' => '+79876543210',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'hash' => md5($user->updated_at->toString()),
        ], $user->toArray());

        $this->assertEquals(0, $user->positions->count());

        $this->assertModelMissing($user->info);

        $this->expectException(\InvalidArgumentException::class);

        $user->setStatus(0);
    }
}
