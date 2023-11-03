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

class ApiResponseNotFoundTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function test_http_response_not_found(): void
    {
        $request = new Request();
        $response = ApiResponse::notFound('Test error');
        $result = $response->toResponse($request);

        $this->assertEquals(404, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
            ], JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function test_http_response_not_found_test(): void
    {
        $request = new Request();
        $response = ApiResponse::notFound('Test error')->payload(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertEquals(404, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
                'payload' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
