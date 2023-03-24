<?php
declare(strict_types=1);

namespace Tests\Http\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseValidationErrorTest extends TestCase
{
    public function test_http_response_validation_error(): void
    {
        $request = new Request();
        $response = ApiResponse::validationError(['field' => 'error'], 'Error');
        $result = $response->toResponse($request);

        $this->assertEquals(422, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'errors' => ['field' => 'error'],
                'message' => 'Error',
            ], JSON_THROW_ON_ERROR)
        );
    }
    public function test_http_response_validation_error_no_message(): void
    {
        $request = new Request();
        $response = ApiResponse::validationError(['field' => 'error'], null);
        $result = $response->toResponse($request);

        $this->assertEquals(422, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'errors' => ['field' => 'error'],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
