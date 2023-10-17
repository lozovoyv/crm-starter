<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Builders;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Collection;

class PermissionGroupBuilder extends Builder
{
    /**
     * Append permissions count.
     *
     * @return $this
     */
    public function withCountPermissions(): self
    {
        $this->withCount('permissions');

        return $this;
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
        $filters = Casting::castArray($filters, ['active' => Casting::bool]);

        if (isset($filters['active'])) {
            $this->where('active', $filters['active']);
        }

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
        $terms = $this->explodeSearchTerms($search, true);

        if (!empty($terms)) {
            $this->where(function (PermissionGroupBuilder $query) use ($terms) {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('id', 'like', "%$term%")
                        ->orWhere('name', 'like', "%$term%");
                }
            });
        }

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
    public function order(string $orderBy = 'order', string $order = 'asc'): self
    {
        switch ($orderBy) {
            case 'active':
            case 'updated_at':
            case 'name':
                $this->query->orderBy($orderBy, $order);
                break;
            default:
                $this->query->orderBy('id', $order);
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Collection<PermissionGroup>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
    }

    /**
     * @param array $columns
     *
     * @return PermissionGroup|null
     */
    public function first($columns = ['*']): ?PermissionGroup
    {
        /** @var PermissionGroup|null $group */
        $group = parent::first($columns);

        return $group;
    }
}
