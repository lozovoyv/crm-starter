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
}
