<?php
declare(strict_types=1);

namespace Tests\Unit\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HelperTraits\CreatesUser;
use Tests\TestCase;

class UserInfoTest extends TestCase
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
        $this->assertModelMissing($user->info);

        $user->info->save();

        $this->assertModelExists($user->info);
        $this->assertTrue($user->is($user->info->user));
    }
}