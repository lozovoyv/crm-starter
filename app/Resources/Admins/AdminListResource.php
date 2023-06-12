<?php
declare(strict_types=1);

namespace App\Resources\Admins;

use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use App\Resources\ListSearchableResource;
use App\Resources\Users\UserListResource;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;

class AdminListResource extends ListSearchableResource
{
    protected array $titles = [
        'id' => 'ID',
        'active' => null,
        'name' => 'ФИО',
        'email' => 'Адрес электронной почты',
        'phone' => 'Телефон',
        'created_at' => 'Создан',
        'updated_at' => 'Изменён',
    ];

    protected array $orderableColumns = ['id', 'name', 'username', 'email', 'phone', 'created_at', 'updated_at'];

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->query = Position::query()
            ->with(['status', 'user'])
            ->where('type_id', PositionType::admin)
            ->leftJoin('users', 'users.id', 'positions.user_id')
            ->select(['positions.*']);
    }

    /**
     * Apply filters.
     *
     * @param array|null $filters
     *
     * @return $this
     */
    public function filter(?array $filters): self
    {
        $filters = $this->castFilters($filters, ['status_id' => Casting::int]);

        if (isset($filters['status_id'])) {
            $this->query->where('positions.status_id', $filters['status_id']);
        }

        parent::filter($filters);

        return $this;
    }

    /**
     * Apply search.
     *
     * @param string|null $search
     *
     * @return $this
     */
    public function search(?string $search): self
    {
        $terms = $this->explodeSearch($search);

        if (!empty($terms)) {
            $this->query->where(function (Builder $query) use ($terms) {
                foreach ($terms as $term) {
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

        parent::search($search);

        return $this;
    }

    /**
     * Apply order.
     *
     * @param string $orderBy
     * @param string $order
     *
     * @return $this
     */
    public function order(string $orderBy = 'name', string $order = 'asc'): self
    {
        switch ($orderBy) {
            case 'id':
            case 'created_at':
            case 'updated_at':
                $this->query->orderBy('positions.' . $orderBy, $order);
                break;
            case 'email':
            case 'phone':
                $this->query->orderBy('users.' . $orderBy, $order);
                break;
            case 'name':
            default:
                $orderBy = 'name';
                $this->query->orderBy('users.lastname', $order);
        }

        parent::order($orderBy, $order);

        return $this;
    }

    /**
     * Format record.
     *
     * @param Position $position
     *
     * @return array
     * @noinspection DuplicatedCode
     */
    public static function format(Position $position): array
    {
        return [
            'id' => $position->id,
            'active' => $position->hasStatus(PositionStatus::active),
            'created_at' => $position->created_at,
            'updated_at' => $position->updated_at,
            'hash' => $position->getHash(),
            'user' => UserListResource::format($position->user),
        ];
    }
}
