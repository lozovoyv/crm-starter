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

use App\Models\History\History;
use App\Utils\Casting;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Collection;

class HistoryBuilder extends Builder
{
    /**
     * Filter by entry type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function whereEntryType(string $type): self
    {
        $this->where('entry_type', $type);

        return $this;
    }

    /**
     * Filter by entry ID.
     *
     * @param int $ID
     *
     * @return $this
     */
    public function whereEntryID(int $ID): self
    {
        $this->where('entry_id', $ID);

        return $this;
    }

    /**
     * Filter by entry tags.
     *
     * @param array $tags
     *
     * @return $this
     */
    public function whereEntryTags(array $tags): self
    {
        $this->whereIn('entry_tag', $tags);

        return $this;
    }

    /**
     * Filter by ID.
     *
     * @param int $historyID
     *
     * @return $this
     */
    public function whereID(int $historyID): self
    {
        $this->where('id', $historyID);

        return $this;
    }

    /**
     * Filter by operator.
     *
     * @param int|null $positionID
     *
     * @return $this
     */
    public function whereOperator(?int $positionID): self
    {
        $this->where('position_id', $positionID);

        return $this;
    }

    /**
     * Append changes.
     *
     * @return $this
     */
    public function withChanges(): self
    {
        $this->with('changes');

        return $this;
    }

    /**
     * Append comments.
     *
     * @return $this
     */
    public function withComments(): self
    {
        $this->with('comments');

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
        $filters = Casting::castArray($filters, ['action_ids' => Casting::array]);

        if (isset($filters['action_ids'])) {
            $this->whereIn('action_id', $filters['action_ids']);
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
    public function order(string $orderBy = 'timestamp', string $order = 'asc'): self
    {
        $this->orderBy($orderBy, $order);

        return $this;
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string|Expression  $column
     *
     * @return $this
     */
    public function latest($column = 'id'): HistoryBuilder
    {
        return parent::latest($column);
    }

    /**
     * @param array $columns
     *
     * @return Collection<History>
     */
    public function get($columns = ['*']): Collection
    {
        return parent::get($columns);
    }

    /**
     * @param array $columns
     *
     * @return History|null
     */
    public function first($columns = ['*']): ?History
    {
        /** @var History|null $history */
        $history = parent::first($columns);

        return $history;
    }
}
