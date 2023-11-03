<?php
declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\APIRequest;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class APIRequestTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function test_api_request_rules(): void
    {
        /** @var APIRequest $request */
        $request = app()->make(APIRequest::class);

        $this->assertEquals([], $request->rules());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_request_hash(): void
    {
        /** @var APIRequest $request */
        $request = app()->make(APIRequest::class);

        $this->assertNull($request->hash());

        $request->merge(['hash' => 'test']);
        $this->assertEquals('test', $request->hash());
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_request_data(): void
    {
        /** @var APIRequest $request */
        $request = app()->make(APIRequest::class);

        $this->assertEquals([], $request->data());

        $request->merge(['data' => ['test' => '123']]);
        $this->assertEquals(['test' => '123'], $request->data());

        $request->merge(['data' => ['test' => '123', 'test2' => '456']]);
        $this->assertEquals(['test' => '123', 'test2' => '456'], $request->data());

        $this->assertEquals(['test2' => '456'], $request->data(['test2']));

        $request->merge(['data' => ['test.a' => '123', 'test.b' => '456']]);
        $this->assertEquals(['test' => ['a' => '123', 'b' => '456']], $request->data(['test']));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_request_integer(): void
    {
        /** @var APIRequest $request */
        $request = app()->make(APIRequest::class);

        $this->assertNull($request->integer('int'));

        $request->merge(['int' => 555]);
        $this->assertEquals(555, $request->integer('int'));

        $request->merge(['int' => -42]);
        $this->assertEquals(-42, $request->integer('int'));

        $request->merge(['int' => 0]);
        $this->assertEquals(0, $request->integer('int'));

        $request->merge(['int' => null]);
        $this->assertNull($request->integer('int'));
    }
}
