<?php
declare(strict_types=1);

namespace App\Resources\Permissions;

use App\Models\Permissions\Permission;
use App\Resources\ListSearchableResource;
use Illuminate\Database\Eloquent\Builder;

class PermissionListResource extends ListSearchableResource
{
    protected array $titles = [
        'scope' =>'Модуль',
        'name' => 'Название',
        'description' => 'Описание',
        'key' => 'Ключ',
    ];

    protected array $orderableColumns = ['id', 'name', 'updated_at'];

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->query = Permission::query()->with('scope')->orderBy('order');

        parent::order('order', 'asc');
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
        parent::filter([]);

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
                        ->orWhere('key', 'like', "%$term%")
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
        parent::order('order', 'asc');

        return $this;
    }

    /**
     * Format record.
     *
     * @param Permission $permission
     *
     * @return array
     * @noinspection DuplicatedCode
     */
    public static function format(Permission $permission): array
    {
        return [
            'id' => $permission->id,
            'key' => $permission->key,
            'scope' => $permission->scope->name,
            'name' => $permission->name,
            'description' => $permission->description,
        ];
    }
}
