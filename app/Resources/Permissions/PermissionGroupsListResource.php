<?php
declare(strict_types=1);

namespace App\Resources\Permissions;

use App\Models\Permissions\PermissionGroup;
use App\Resources\ListSearchableResource;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;

class PermissionGroupsListResource extends ListSearchableResource
{
    protected array $titles = [
        'id' => 'ID',
        'state' => null,
        'name' => 'Название',
        'count' => 'Права',
        'description' => 'Описание',
        'updated_at' => 'Изменено',
    ];

    protected array $orderableColumns = ['id', 'name', 'updated_at'];

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->query = PermissionGroup::query()->withCount('permissions');
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
        $filters = $this->castFilters($filters, ['active' => Casting::bool]);

        if (isset($filters['active'])) {
            $this->query->where('active', $filters['active']);
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
                        ->orWhere('name', 'like', "%$term%");
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
            case 'active':
            case 'updated_at':
            case 'name':
                $this->query->orderBy($orderBy, $order);
                break;
            default:
                $orderBy = 'id';
                $this->query->orderBy('id', $order);
        }

        parent::order($orderBy, $order);

        return $this;
    }

    /**
     * Format record.
     *
     * @param PermissionGroup $permissionGroup
     *
     * @return array
     * @noinspection DuplicatedCode
     */
    public static function format(PermissionGroup $permissionGroup): array
    {
        $permissionsCount = $permissionGroup->getAttribute('permissions_count') ?? $permissionGroup->permissions()->count();

        return [
            'id' => $permissionGroup->id,
            'name' => $permissionGroup->name,
            'count' => $permissionsCount,
            'description' => $permissionGroup->description,
            'active' => $permissionGroup->active,
            'locked' => $permissionGroup->locked,
            'updated_at' => $permissionGroup->updated_at,
            'hash' => $permissionGroup->getHash(),
        ];
    }
}
