<?php
declare(strict_types=1);

namespace Tests\Feature\API\Auth;

use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_api_auth_admin_success_login_email(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::admin);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'test@mail.ru',
            'password' => 'test',
        ]);

        $response->assertOk();
    }

    public function test_api_auth_admin_success_login_username(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::admin);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'tester',
            'password' => 'test',
        ]);

        $response->assertOk();
    }

    public function test_api_auth_admin_login_wrong_username(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::admin);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'testa',
            'password' => 'test',
        ]);

        $response->assertUnprocessable();
    }

    public function test_api_auth_admin_login_wrong_email(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::admin);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'testa@mail.ru',
            'password' => 'test',
        ]);

        $response->assertUnprocessable();
    }

    public function test_api_auth_admin_login_wrong_password(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::admin);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'test@mail.ru',
            'password' => 'testa',
        ]);

        $response->assertUnprocessable();
    }

    public function test_api_auth_staff_success_login(): void
    {
        $user = $this->createUser('testing', 'user', null, 'test@mail.ru', 'tester', null, null, 'test');
        $position = $this->createPosition($user, PositionType::staff);

        $response = $this->apiActingAs(null)->post('/api/auth/login', [
            'username' => 'test@mail.ru',
            'password' => 'test',
        ]);

        $response->assertOk();
    }
}
