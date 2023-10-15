<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Admins;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\Position;
use App\Resources\Admins\AdminListResource;

class AdminListController extends ApiController
{
    /**
     * Get users list.
     *
     * @param APIListRequest $request
     * @param AdminListResource $resource
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request, AdminListResource $resource): ApiResponse
    {
        $positions = $resource
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy(), $request->orderDirection())
            ->paginate($request->page(), $request->perPage());

        $positions->transform(function (Position $position) {
            return AdminListResource::format($position);
        });

        return ApiResponse::list($positions)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }
}
