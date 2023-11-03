<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use JsonException;
use Tests\TestCase;

class ApiResponseValidationErrorTest extends TestCase
{
    /**
     * @throws JsonException
     */
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

    /**
     * @throws JsonException
     */
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
