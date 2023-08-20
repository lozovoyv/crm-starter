<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use App\Utils\Casting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use ReflectionClass;
use ReflectionException;
use Tests\Assets\Resources\ListResource;
use Tests\TestCase;

class ListResourceTest extends TestCase
{
    public function test_filter(): void
    {
        $resource = new ListResource();
        $resource->filter(['test' => 123]);
        $this->assertEquals(['test' => 123], $resource->getFilters());
    }

    public function test_order(): void
    {
        $resource = new ListResource();
        $resource->order('test', 'asc');
        $this->assertEquals('test', $resource->getOrderBy());
        $this->assertEquals('asc', $resource->getOrder());
    }

    public function test_get_titles(): void
    {
        $resource = new ListResource();
        $resource->setTitles(['test' => 'test title']);
        $this->assertEquals(['test' => 'test title'], $resource->getTitles());
    }

    public function test_orderable_columns(): void
    {
        $resource = new ListResource();
        $resource->setOrderableColumns(['id', 'test']);
        $this->assertEquals(['id', 'test'], $resource->getOrderableColumns());
    }

    /**
     * @throws ReflectionException
     */
    public function test_cast_filters(): void
    {
        $resourceReflection = new ReflectionClass(ListResource::class);
        $method = $resourceReflection->getMethod('castFilters');

        $resource = new ListResource();
        $casted = $method->invokeArgs($resource, [
            [
                'id' => '123',
                'name' => 'test',
            ], [
                'id' => Casting::int,
                'name' => Casting::string,
            ],
        ]);

        $this->assertEquals(['id' => 123, 'name' => 'test'], $casted);
    }

    public function test_paginate(): void
    {
        $resource = new ListResource();
        $this->assertInstanceOf(LengthAwarePaginator::class, $resource->paginate(1, 10));
    }

    public function test_get(): void
    {
        $resource = new ListResource();
        $this->assertInstanceOf(Collection::class, $resource->get());
    }
}
