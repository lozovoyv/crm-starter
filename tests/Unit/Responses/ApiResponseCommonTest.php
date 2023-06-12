<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseCommonTest extends TestCase
{
    public function test_http_response_common(): void
    {
        $request = new Request();
        $response = ApiResponse::common([]);
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'data' => [],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_last_modified(): void
    {
        $now = Carbon::now();

        $request = new Request();
        $response = ApiResponse::common([])->lastModified($now);
        $result = $response->toResponse($request);

        $this->assertTrue($result->headers->has('Last-Modified'));

        $timestamp = Carbon::parse($result->headers->get('Last-Modified'));
        $this->assertTrue($timestamp->is($now->setTimezone('GMT')->toString()), $timestamp->toString() . ' != ' . $now->toString());
    }

    public function test_http_response_common_message(): void
    {
        $request = new Request();
        $response = ApiResponse::common([])->message('Test');
        $result = $response->toResponse($request);

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'data' => [],
                'message' => 'Test',
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_payload(): void
    {
        $request = new Request();
        $response = ApiResponse::common([])->payload(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'data' => [],
                'payload' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_array(): void
    {
        $request = new Request();
        $response = ApiResponse::common(['test' => 123]);
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(),
            json_encode([
                'data' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_arrayable(): void
    {
        $request = new Request();
        $response = ApiResponse::common(collect(['test' => 123]));
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(),
            json_encode([
                'data' => ['test' => 123],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_null(): void
    {
        $request = new Request();
        $response = ApiResponse::common(null);
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(),
            json_encode([
                'data' => null,
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_bool(): void
    {
        $request = new Request();
        $response = ApiResponse::common(true);
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(),
            json_encode([
                'data' => true,
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_common_string(): void
    {
        $request = new Request();
        $response = ApiResponse::common('test');
        $result = $response->toResponse($request);

        $this->assertJsonStringEqualsJsonString(
            $result->content(),
            json_encode([
                'data' => 'test',
            ], JSON_THROW_ON_ERROR)
        );
    }
}
