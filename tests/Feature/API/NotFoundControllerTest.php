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

namespace Tests\Feature\API;

use App\Http\Responses\ApiResponse;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_api_not_found_response(): void
    {
        $response = $this->apiActingAs(PositionType::admin, [])->get('/api/testing_not_found_method');

        $this->assertEquals(ApiResponse::CODE_NOT_FOUND, $response->status());
    }
}
