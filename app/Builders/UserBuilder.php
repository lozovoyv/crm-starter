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

use App\Models\Users\User;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Collection;

class UserBuilder extends Builder
{
    /**
     * Apply filters.
     *
     * @param array|null $filters
     *
     * @return $this
     */
    public function filter(?array $filters): self
    {
        $filters = Casting::castArray($filters, ['status_id' => Casting::int]);

        if (isset($filters['status_id'])) {
            $this->where('status_id', $filters['status_id']);
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
            $this->where(function (UserBuilder $query) use ($terms) {
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
                $this->orderBy($orderBy, $order);
                break;
            case 'name':
            default:
                $orderBy = 'name';
                $this->orderBy('lastname', $order);
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Collection<User>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
    }

    /**
     * @param array $columns
     *
     * @return User|null
     */
    public function first($columns = ['*']): ?User
    {
        /** @var User|null $user */
        $user = parent::first($columns);

        return $user;
    }
}
