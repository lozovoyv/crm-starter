<?php
declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use TypeError;

class ApiResponseListTest extends TestCase
{
    public function test_http_response_list(): void
    {
        $request = new Request();
        $response = ApiResponse::list();
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'list' => [],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_list_array(): void
    {
        $request = new Request();
        $response = ApiResponse::list([1, 2, 3]);
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'list' => [1, 2, 3],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_list_collection(): void
    {
        $request = new Request();
        $response = ApiResponse::list()->items(collect([1, 2, 3]));
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'list' => [1, 2, 3],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_list_pagination(): void
    {
        $request = new Request();
        $paginator = new LengthAwarePaginator(collect([1, 2, 3]), 3, 3, 1);
        $response = ApiResponse::list()->items($paginator);
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'list' => [1, 2, 3],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 1,
                    'to' => 3,
                    'total' => 3,
                    'per_page' => 3,
                ],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_list_options(): void
    {
        $request = new Request();
        $paginator = new LengthAwarePaginator(collect([1, 2, 3]), 3, 3, 1);
        $response = ApiResponse::list()
            ->items($paginator)
            ->title('test title')
            ->titles(['column1' => 'column1title', 'column2' => 'column2title'])
            ->filters(['status' => 'test'])
            ->search('search')
            ->order('field', 'asc')
            ->orderable(['field', 'field2'])
            ->message('message')
            ->payload(['payload']);
        $result = $response->toResponse($request);

        $this->assertEquals(200, $result->status());

        $this->assertJson($result->content());

        $this->assertJsonStringEqualsJsonString(
            $result->content(), json_encode([
                'list' => [1, 2, 3],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 1,
                    'to' => 3,
                    'total' => 3,
                    'per_page' => 3,
                ],
                'title' => 'test title',
                'titles' => ['column1' => 'column1title', 'column2' => 'column2title'],
                'filters' => ['status' => 'test'],
                'search' => 'search',
                'order_by' => 'field',
                'order' => 'asc',
                'orderable' => ['field', 'field2'],
                'message' => 'message',
                'payload' => ['payload'],
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_http_response_list_invalid(): void
    {
        $this->expectException(TypeError::class);
        ApiResponse::list()->items('error');
    }
}
