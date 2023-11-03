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
use JsonException;
use Tests\TestCase;

class ApiResponseSuccessTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function test_http_response_success(): void
    {
        $request = new Request();
        $response = ApiResponse::success('Test success');
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test success',
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function test_http_response_success_payload(): void
    {
        $request = new Request();
        $response = ApiResponse::success('Test success')->payload(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test success',
                'payload' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
