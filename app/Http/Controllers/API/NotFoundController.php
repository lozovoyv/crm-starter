<?php

namespace App\Http\Controllers\API;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotFoundController extends ApiController
{
    public function notFound(Request $request): JsonResponse
    {
        return APIResponse::notFound('Метод API ' . $request->path() . ' не найден');
    }
}
