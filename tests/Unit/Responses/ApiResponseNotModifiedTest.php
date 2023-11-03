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

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ApiResponseNotModifiedTest extends TestCase
{
    public function test_http_response_not_modified(): void
    {
        $request = new Request();
        $response = ApiResponse::notModified();
        $result = $response->toResponse($request);

        $this->assertEquals(304, $result->status());

        $this->assertEquals('{}', $result->content());
    }
}
