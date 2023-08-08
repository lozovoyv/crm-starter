<?php
declare(strict_types=1);

namespace Tests\Feature\API\Auth;

use App\Http\Responses\ApiResponse;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthCurrentTest extends TestCase
{
    public function test_api_auth_current_unauthenticated(): void
    {
        $response = $this->apiActingAs(null)->get('/api/auth/current');

        $response->assertJson(['data' => ['user' => null, 'permissions' => []]]);
    }
}
