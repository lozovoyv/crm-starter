<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ApiResponseErrorTest extends TestCase
{
    public function test_http_response_error(): void
    {
        $request = new Request();
        $response = ApiResponse::error('Test error');
        $result = $response->toResponse($request);

        $this->assertEquals(400, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error',
            ], JSON_THROW_ON_ERROR)
        );
    }
}
