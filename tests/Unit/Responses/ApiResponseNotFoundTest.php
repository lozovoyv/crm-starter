<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseNotFoundTest extends TestCase
{
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
