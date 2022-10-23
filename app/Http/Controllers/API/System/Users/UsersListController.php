<?php

namespace App\Http\Controllers\API\System\Users;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class UsersListController extends ApiController
{
    protected array $defaultFilters = [
        'status_id' => null,
    ];

    protected array $titles = [
        'id' => 'ID',
        'active' => null,
        'name' => 'ФИО',
        'username' => 'Пользователь',
        'email' => 'Email',
        'phone' => 'Телефон',
        'created_at' => 'Создан',
        'updated_at' => 'Изменён',
    ];

    protected array $ordering = ['id', 'name', 'username', 'email', 'phone', 'created_at', 'updated_at'];

    /**
     * Get users list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = User::query();

        // apply order
        $order = $request->order();
        $orderBy = $request->orderBy('lastname');
        switch ($orderBy) {
            case 'id':
            case 'username':
            case 'email':
            case 'phone':
            case 'created_at':
            case 'updated_at':
                $query->orderBy($orderBy, $order);
                break;
            case 'name':
            default:
                $query->orderBy('lastname', $order);
        }

        // apply filters
        $filters = $request->filters($this->defaultFilters);
        if (isset($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        // apply search
        $search = $request->search();
        if (!empty($search)) {
            $query->where(function (Builder $query) use ($search) {
                foreach ($search as $term) {
                    $query
                        ->orWhere('id', 'like', "%$term%")
                        ->orWhere('lastname', 'like', "%$term%")
                        ->orWhere('firstname', 'like', "%$term%")
                        ->orWhere('patronymic', 'like', "%$term%")
                        ->orWhere('username', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%")
                        ->orWhere('phone', 'like', "%$term%");
                }
            });
        }

        $users = $request->paginate($query);

        return APIResponse::list(
            $users,
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
