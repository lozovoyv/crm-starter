<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Models\Permissions\PermissionRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class RolesListController extends ApiController
{
    protected array $defaultFilters = [
        'active' => null,
    ];

    protected array $titles = [
        'id' => 'ID',
        'state' => null,
        'name' => 'Название',
        'count' => 'Права',
        'description' => 'Описание',
        'updated_at' => 'Изменено',
    ];

    protected array $ordering = ['id', 'state', 'name', 'updated_at'];

    /**
     * Get roles list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = PermissionRole::query()
            ->withCount(['permissions']);

        // apply order
        $order = $request->order();
        $orderBy = $request->orderBy('id');
        switch ($orderBy) {
            case 'active':
            case 'updated_at':
            case 'name':
                $query->orderBy($orderBy, $order);
                break;
            default:
                $query->orderBy('id', $order);
        }

        // apply filters
        $filters = $request->filters($this->defaultFilters);
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        // apply search
        $search = $request->search();
        if (!empty($search)) {
            $query->where(function (Builder $query) use ($search) {
                foreach ($search as $term) {
                    $query
                        ->orWhere('id', 'like', "%$term%")
                        ->orWhere('name', 'like', "%$term%");
                }
            });
        }

        $roles = $request->paginate($query);

        return APIResponse::list(
            $roles,
            $this->titles,
            $filters,
            $this->defaultFilters,
            $request->search(true),
            $order,
            $orderBy,
            $this->ordering
        );
    }
}
