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
use Illuminate\Database\Eloquent\Collection;

class PermissionBuilder extends Builder
{
    /**
     * Append scope.
     *
     * @return $this
     */
    public function withScope(): self
    {
        $this->with('scope');

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
        if (isset($filters['scope_name'])) {
            $scopes = !is_array($filters['scope_name']) ? [$filters['scope_name']] : $filters['scope_name'];

            $this->whereIn('scope_name', $scopes);
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
            $this->where(function (PermissionBuilder $query) use ($terms) {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('key', 'like', "%$term%")
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
        $this->orderBy($orderBy, $order)->orderBy('order', 'asc');

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Collection<Permission>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Permission|null
     */
    public function first($columns = ['*']): ?Permission
    {
        /** @var Permission|null $history */
        $history = parent::first($columns);

        return $history;
    }
}
