<?php
declare(strict_types=1);

namespace Tests\Http\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseForbiddenTest extends TestCase
{
    public function test_http_response_forbidden(): void
    {
        $request = new Request();
        $response = ApiResponse::forbidden('Test error');
        $result = $response->toResponse($request);

        $this->assertEquals(403, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_forbidden_payload(): void
    {
        $request = new Request();
        $response = ApiResponse::forbidden('Test error')->payload(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertEquals(403, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
                'payload' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
