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

namespace Tests\Feature\API\System\Permissions;

use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionsHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_permissions_groups_history(): void
    {
        $response = $this->apiActingAs(PositionType::staff, [Permission::system__permissions])->get('/api/system/permissions/history');

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
    }

    public function test_permissions_groups_history_no_permission(): void
    {
        $response = $this->apiActingAs(PositionType::staff, [])->get('/api/system/permissions/history');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }
}
