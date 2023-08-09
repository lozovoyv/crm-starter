<?php
declare(strict_types=1);

namespace Tests\Feature\API\Auth;

use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_api_auth_admin_logout(): void
    {
        $response = $this->apiActingAs(PositionType::admin)->post('/api/auth/logout', []);

        $response->assertOk();
    }

    public function test_api_auth_staff_logout(): void
    {
        $response = $this->apiActingAs(PositionType::staff)->post('/api/auth/logout', []);

        $response->assertOk();
    }
}
