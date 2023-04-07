<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseNotModifiedTest extends TestCase
{
    public function test_http_response_not_modified(): void
    {
        $request = new Request();
        $response = ApiResponse::notModified();
        $result = $response->toResponse($request);

        $this->assertEquals(304, $result->status());

        $this->assertEquals('{}', $result->content());
    }
}
