<?php
declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\APIListRequest;
use App\Models\Users\User;
use App\Utils\Casting;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class APIListRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_paginate(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $paginator = $request->paginate(User::query());

        $this->assertEquals(10, $paginator->perPage());
        $this->assertEquals(1, $paginator->currentPage());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_page(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $request->merge([
            'page' => 5,
            'page_custom' => 9,
        ]);

        $this->assertEquals(5, $request->page());
        $this->assertEquals(9, $request->page('page_custom'));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_per_page(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $request->merge([
            'per_page' => 5,
            'per_page_custom' => 9,
        ]);

        $this->assertEquals(5, $request->perPage());
        $this->assertEquals(9, $request->perPage(10, 'per_page_custom'));
        $this->assertEquals(15, $request->perPage(15, 'per_page_custom_default'));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_rules(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $this->assertEquals([], $request->rules());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_filters(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);
        $this->assertEquals([], $request->filters());

        $filters = $request->filters(['int' => Casting::int, 'bool' => Casting::bool, 'array' => Casting::array, 'date' => Casting::datetime]);
        $this->assertNull($filters['int']);
        $this->assertNull($filters['bool']);
        $this->assertNull($filters['array']);
        $this->assertNull($filters['date']);

        // GET params are always string
        $request->merge(['filters' => [
            'string' => 'test',
            'int' => '12',
            'bool' => 'false',
            'array' => '[1,2,3]',
            'date' => '2023-04-14',
        ]]);

        $filters = $request->filters(['int' => Casting::int, 'bool' => Casting::bool, 'array' => Casting::array, 'date' => Casting::datetime]);

        $this->assertEquals('test', $filters['string']);
        $this->assertEquals(12, $filters['int']);
        $this->assertEquals(false, $filters['bool']);
        $this->assertEquals([1, 2, 3], $filters['array']);
        $this->assertEquals('2023-04-14', $filters['date']->format('Y-m-d'));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_search(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);
        $this->assertEquals([], $request->search());

        $request->merge(['search' => 'test']);
        $this->assertEquals(['test'], $request->search());

        $request->merge(['search' => 'test string    input']);
        $this->assertEquals(['test', 'string', 'input'], $request->search());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_order(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $request->merge(['order' => 'ASC']);
        $this->assertEquals('asc', $request->order());

        $request->merge(['order' => 'desc']);
        $this->assertEquals('desc', $request->order());

        $request->merge(['order' => 'test']);
        $this->assertEquals('asc', $request->order());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_list_request_order_by(): void
    {
        /** @var APIListRequest $request */
        $request = app()->make(APIListRequest::class);

        $this->assertEquals('default', $request->orderBy('default'));

        $request->merge(['order_by' => 'test']);
        $this->assertEquals('test', $request->orderBy('default'));
    }
}
