<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use Illuminate\Database\Eloquent\Builder;

class StaffListController extends ApiController
{
    protected array $defaultFilters = [
        'status_id' => null,
    ];

    protected array $titles = [
        'id' => 'ID',
        'active' => null,
        'name' => 'ФИО',
        'username' => 'Пользователь',
        'email' => 'Адрес электронной почты',
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
     * @return  ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $query = Position::query()
            ->with(['status', 'user'])
            ->where('type_id', PositionType::staff)
            ->leftJoin('users', 'users.id', 'positions.user_id')
            ->select(['positions.*']);

        // apply order
        $order = $request->order();
        $orderBy = $request->orderBy('lastname');
        switch ($orderBy) {
            case 'id':
            case 'positions.created_at':
            case 'positions.updated_at':
                $query->orderBy($orderBy, $order);
                break;
            case 'username':
            case 'email':
            case 'phone':
                $query->orderBy('users.' . $orderBy, $order);
                break;
            case 'name':
            default:
                $query->orderBy('users.lastname', $order);
        }

        // apply filters
        $filters = $request->filters($this->defaultFilters);
        if (isset($filters['status_id'])) {
            $query->where('positions.status_id', $filters['status_id']);
        }

        // apply search
        $search = $request->search();
        if (!empty($search)) {
            $query->where(function (Builder $query) use ($search) {
                foreach ($search as $term) {
                    $query
                        ->orWhere('positions.id', 'like', "%$term%")
                        ->orWhere('users.lastname', 'like', "%$term%")
                        ->orWhere('users.firstname', 'like', "%$term%")
                        ->orWhere('users.patronymic', 'like', "%$term%")
                        ->orWhere('users.username', 'like', "%$term%")
                        ->orWhere('users.email', 'like', "%$term%")
                        ->orWhere('users.phone', 'like', "%$term%");
                }
            });
        }

        $staff = $request->paginate($query);

        return ApiResponse::list()
            ->items($staff)
            ->titles($this->titles)
            ->filters($filters, $this->defaultFilters)
            ->search($request->search(true))
            ->order($orderBy, $order)
            ->orderable($this->ordering);
    }
}
