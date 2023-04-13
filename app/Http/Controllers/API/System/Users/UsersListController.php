<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;

class UsersListController extends ApiController
{
    protected array $titles = [
        'id' => 'ID',
        'name' => 'ФИО',
        'username' => 'Логин',
        'email' => 'Электронная почта',
        'phone' => 'Телефон',
        'created_at' => 'Создана',
        'updated_at' => 'Изменена',
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
                $orderBy = 'name';
                $query->orderBy('lastname', $order);
        }

        // apply filters
        $filters = $request->filters(['status_id' => Casting::int]);
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
                        ->orWhere('display_name', 'like', "%$term%")
                        ->orWhere('username', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%")
                        ->orWhere('phone', 'like', "%$term%");
                }
            });
        }

        $users = $request->paginate($query);

        return ApiResponse::list()
            ->items($users)
            ->titles($this->titles)
            ->order($orderBy, $order)
            ->orderable($this->ordering);
    }
}
