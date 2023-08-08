<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ApiResponseSuccessTest extends TestCase
{
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
