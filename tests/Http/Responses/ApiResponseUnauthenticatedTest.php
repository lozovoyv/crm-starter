<?php
declare(strict_types=1);

namespace Tests\Http\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseUnauthenticatedTest extends TestCase
{
    public function test_http_response_error(): void
    {
        $request = new Request();
        $response = ApiResponse::unauthenticated('Test error');
        $result = $response->toResponse($request);

        $this->assertEquals(401, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'message' => 'Test error'
            ], JSON_THROW_ON_ERROR)
        );
    }
}
