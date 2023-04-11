<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;

class PermissionGroupsListController extends ApiController
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

    protected array $ordering = ['id', 'name', 'updated_at'];

    /**
     * Get permission groups list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $query = PermissionGroup::query()->withCount(['permissions']);

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
        $filters = $request->filters([
            'active' => Casting::bool,
        ], $this->defaultFilters);

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

        $permissionGroups = $request->paginate($query);

        return ApiResponse::list()
            ->items($permissionGroups)
            ->titles($this->titles)
            ->order($orderBy, $order)
            ->orderable($this->ordering);
    }
}
