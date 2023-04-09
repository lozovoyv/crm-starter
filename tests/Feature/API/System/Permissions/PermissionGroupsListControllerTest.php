<?php
declare(strict_types=1);

namespace Tests\Feature\API\System\Permissions;

use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HelperTraits\CreatesPosition;
use Tests\HelperTraits\CreatesUser;
use Tests\TestCase;

class PermissionGroupsListControllerTest extends TestCase
{
    use RefreshDatabase, CreatesPosition, CreatesUser;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_permission_groups_list(): void
    {
        $user = $this->createUser('base', 'position', 'test');
        $position = $this->createPosition($user, PositionType::staff);
        $position->permissions()->sync([Permission::get('system.permissions')->id]);

        $response = $this->getApi('/api/system/permissions/groups', $user, ['position_id', $position->id]);

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
    }

    public function test_permission_groups_list_no_permission(): void
    {
        $user = $this->createUser('base', 'position', 'test');
        $position = $this->createPosition($user, PositionType::staff);

        $response = $this->getApi('/api/system/permissions/groups', $user, ['position_id', $position->id]);

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }
}
