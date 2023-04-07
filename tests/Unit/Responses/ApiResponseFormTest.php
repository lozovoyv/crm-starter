<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiResponseFormTest extends TestCase
{
    public function test_http_response_form(): void
    {
        $request = new Request();
        $response = ApiResponse::form();
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'values' => [],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_form_content(): void
    {
        $request = new Request();
        $response = ApiResponse::form()
            ->values(['field' => 123])
            ->title('test')
            ->titles(['field' => 'Field'])
            ->rules(['field' => 'required'])
            ->messages(['field' => 'Field error'])
            ->hash('hash')
            ->message('message')
            ->payload(['test_payload']);

        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'values' => ['field' => 123],
                'title' => 'test',
                'titles' => ['field' => 'Field'],
                'rules' => ['field' => 'required'],
                'messages' => ['field' => 'Field error'],
                'hash' => 'hash',
                'message' => 'message',
                'payload' => ['test_payload'],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
