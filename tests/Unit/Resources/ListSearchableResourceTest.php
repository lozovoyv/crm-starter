<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use ReflectionClass;
use ReflectionException;
use Tests\Assets\Resources\ListSearchableResource;
use Tests\TestCase;

class ListSearchableResourceTest extends TestCase
{
    public function test_search(): void
    {
        $resource = new ListSearchableResource();

        $resource->search(null);
        $this->assertNull($resource->getSearch());

        $resource->search('search test   string  ');
        $this->assertEquals('search test   string  ', $resource->getSearch());
    }

    /**
     * @throws ReflectionException
     */
    public function test_explode_search(): void
    {
        $resourceReflection = new ReflectionClass(ListSearchableResource::class);
        $method = $resourceReflection->getMethod('explodeSearch');

        $resource = new ListSearchableResource();
        $exploded = $method->invokeArgs($resource, ['search test   string  ']);

        $this->assertEquals(['search', 'test', 'string'], $exploded);
    }
}
