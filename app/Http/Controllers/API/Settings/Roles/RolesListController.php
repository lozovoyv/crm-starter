<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Models\Permissions\PermissionRole;
use Illuminate\Http\JsonResponse;

class RolesListController extends ApiController
{
    protected array $defaultFilters = [
        'active' => null,
    ];

    protected array $titles = [
        'id' => 'ID',
        'name' => 'Название',
        'description' => 'Описание',
    ];

    /**
     * Get roles list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = PermissionRole::query();

        $filters = $request->filters($this->defaultFilters);

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        $roles = $request->paginate($query);

        $roles->transform(function (PermissionRole $role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'active' => $role->active,
                'locked' => $role->locked,
            ];
        });

        return APIResponse::list(
            $roles,
            $this->titles,
            $filters,
            $this->defaultFilters
        );
    }
}
