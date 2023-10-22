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

use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Utils\Casting;
use Illuminate\Database\Eloquent\Collection;

class PositionBuilder extends Builder
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
            $this->where(function (PositionBuilder $query) use ($terms) {
                foreach ($terms as $term) {
                    $query
                        ->orWhere('id', 'like', "%$term%");
//                        ->orWhereHas('user', function (UserBuilder $query) use ($term) {
//                            $query
//                                ->orWhere('lastname', 'like', "%$term%")
//                                ->orWhere('firstname', 'like', "%$term%")
//                                ->orWhere('patronymic', 'like', "%$term%")
//                                ->orWhere('display_name', 'like', "%$term%")
//                                ->orWhere('username', 'like', "%$term%")
//                                ->orWhere('email', 'like', "%$term%")
//                                ->orWhere('phone', 'like', "%$term%");
//                        });
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
//        switch ($orderBy) {
//            case 'id':
//            case 'email':
//            case 'phone':
//            case 'created_at':
//            case 'updated_at':
//                $this->orderBy($orderBy, $order);
//                break;
//            case 'name':
//            default:
//                $orderBy = 'name';
//                $this->orderBy('lastname', $order);
//        }

        return $this;
    }

    /**
     * Filter admin positions.
     *
     * @return $this
     */
    public function admin(): self
    {
        $this->where('type_id', PositionType::admin);

        return $this;
    }

    /**
     * Filter staff positions.
     *
     * @return $this
     */
    public function staff(): self
    {
        $this->where('type_id', PositionType::staff);

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Collection<Position>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Position|null
     */
    public function first($columns = ['*']): ?Position
    {
        /** @var Position|null $position */
        $position = parent::first($columns);

        return $position;
    }
}
