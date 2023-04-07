<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseTokenMismatchTest extends TestCase
{
    public function test_http_response_token_mismatch(): void
    {
        $request = new Request();
        $response = ApiResponse::tokenMismatch('Test error');
        $result = $response->toResponse($request);

        $this->assertEquals(419, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_token_mismatch_payload(): void
    {
        $request = new Request();
        $response = ApiResponse::tokenMismatch('Test error')->payload(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertEquals(419, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
                'payload' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
