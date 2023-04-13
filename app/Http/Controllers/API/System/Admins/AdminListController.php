<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Admins;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;

class AdminListController extends ApiController
{
    public function list(): ApiResponse
    {
        return ApiResponse::list()->items([]);
    }
}
