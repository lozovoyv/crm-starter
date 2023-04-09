<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PermissionsListController extends ApiController
{
    /**
     * Permissions list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        /** @var Collection $permissions */
        $query = Permission::query()
            ->with('scope')
            ->orderBy('order');

        // apply search
        $search = $request->search();
        if (!empty($search)) {
            $query->where(function (Builder $query) use ($search) {
                foreach ($search as $term) {
                    $query
                        ->orWhere('key', 'like', "%$term%")
                        ->orWhere('name', 'like', "%$term%");
                }
            });
        }

        $permissions = $query->get();

        return ApiResponse::list()
            ->items($permissions)
            ->titles(['Модуль', 'Название', 'Описание', 'Ключ']);
    }
}
