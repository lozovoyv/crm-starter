<?php
declare(strict_types=1);

namespace Tests\Feature\API\System\Permissions;

use App\Http\Responses\ApiResponse;
use App\Models\Positions\PositionType;
use App\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionGroupsListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_permission_groups_list(): void
    {
        $response = $this
            ->apiActingAs(PositionType::staff, [Permissions::system__permissions])
            ->get('/api/system/permissions/groups');

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
    }

    public function test_permission_groups_list_no_permission(): void
    {
        $response = $this
            ->apiActingAs(PositionType::staff, [])
            ->get('/api/system/permissions/groups');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }
}
