<?php
declare(strict_types=1);

namespace Tests\Http\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Tests\TestCase;

class ApiResponseServerErrorTest extends TestCase
{
    public function test_http_response_server_error_debug(): void
    {
        $isDebug = config('app.debug');
        config(['app.debug' => true]);

        $request = new Request();
        $exception = new InvalidArgumentException('Test exception');
        $response = ApiResponse::serverError($exception);
        $result = $response->toResponse($request);

        $this->assertEquals(500, $result->status());

        $this->assertJson($result->content());

        $testResponse = $this->createTestResponse($result);

        $testResponse->assertJsonStructure([
            'message',
            'exception',
            'file',
            'line',
            'trace'
        ]);

        config(['app.debug' => $isDebug]);
    }

    public function test_http_response_server_error_prod(): void
    {
        $isDebug = config('app.debug');
        config(['app.debug' => false]);

        $request = new Request();
        $exception = new InvalidArgumentException('Test exception');
        $response = ApiResponse::serverError($exception);
        $result = $response->toResponse($request);

        $this->assertEquals(500, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Server error',
            ], JSON_THROW_ON_ERROR)
        );

        config(['app.debug' => $isDebug]);
    }
}
