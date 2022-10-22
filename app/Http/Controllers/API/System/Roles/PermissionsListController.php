<?php

namespace App\Http\Controllers\API\System\Roles;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Models\Permissions\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class PermissionsListController extends ApiController
{
    /**
     * Permissions list.
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        /** @var Collection $permissions */
        $query = Permission::query()
            ->with('permissionModule')
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

        return APIResponse::list($permissions, ['Ключ', 'Название', 'Описание', 'Модуль'], null, null, $request->search(true));
    }
}
