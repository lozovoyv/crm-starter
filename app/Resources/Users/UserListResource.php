<?php
declare(strict_types=1);

namespace App\Resources\Users;

use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Resources\ListSearchableResource;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;

class UserListResource extends ListSearchableResource
{
    protected array $titles = [
        'id' => 'users/user.id',
        'name' => 'users/user.name',
        'email' => 'users/user.email',
        'phone' => 'users/user.phone',
        'created_at' => 'users/user.created_at',
        'updated_at' => 'users/user.updated_at',
    ];

    protected array $orderableColumns = ['id', 'name', 'email', 'phone', 'created_at', 'updated_at'];

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->query = User::query()->with('status');
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
            $this->query->where('status_id', $filters['status_id']);
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

        parent::search($search);

        return $this;
    }

    /**
     * Apply order.
     *
     * @param string|null $orderBy
     * @param string|null $order
     *
     * @return $this
     */
    public function order(?string $orderBy = 'name', ?string $order = 'asc'): self
    {
        switch ($orderBy) {
            case 'id':
            case 'email':
            case 'phone':
            case 'created_at':
            case 'updated_at':
                $this->query->orderBy($orderBy, $order);
                break;
            case 'name':
            default:
                $orderBy = 'name';
                $this->query->orderBy('lastname', $order);
        }

        parent::order($orderBy, $order);

        return $this;
    }

    /**
     * Format record.
     *
     * @param User $user
     *
     * @return array
     * @noinspection DuplicatedCode
     */
    public static function format(User $user): array
    {
        return [
            'id' => $user->id,
            'is_active' => $user->hasStatus(UserStatus::active),
            'locked' => $user->locked,
            'status' => $user->status->name,
            'lastname' => $user->lastname,
            'firstname' => $user->firstname,
            'patronymic' => $user->patronymic,
            'display_name' => $user->display_name,
            'username' => $user->username,
            'email' => $user->email,
            'has_password' => !empty($user->password),
            'phone' => $user->phone,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'hash' => $user->getHash(),
        ];
    }
}
