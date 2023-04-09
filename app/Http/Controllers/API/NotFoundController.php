<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class NotFoundController extends ApiController
{
    public function notFound(Request $request): ApiResponse
    {
        return ApiResponse::notFound('Метод API ' . $request->path() . ' не найден');
    }
}
