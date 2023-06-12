<?php
declare(strict_types=1);

namespace App\Resources;

use App\Models\History\History;
use App\Utils\Casting;

abstract class HistoryResource extends ListResource
{
    protected array $titles = [
        'timestamp' => 'history.history.timestamp',
        'action' => 'history.history.action',
        'links' => 'history.history.links',
        'comment' => 'history.history.comment',
        'changes' => 'history.history.changes',
        'position_id' => 'history.history.position',
    ];

    protected array $orderableColumns = ['timestamp'];

    /**
     * Filter by entry ID.
     *
     * @param int $ID
     *
     * @return $this
     */
    public function forEntry(int $ID): self
    {
        $this->query->where('entry_id', $ID);

        return $this;
    }

    /**
     * Filter by entry ID.
     *
     * @param int $positionID
     *
     * @return $this
     */
    public function forOperator(int $positionID): self
    {
        $this->query->where('position_id', $positionID);

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
        $filters = $this->castFilters($filters, ['action_ids' => Casting::array]);

        if (isset($filters['action_ids'])) {
            $this->query->whereIn('action_id', $this->filters['action_ids']);
        }

        parent::filter($filters);

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
        $this->query->orderBy('timestamp', $order);

        parent::order('timestamp', $order);

        return $this;
    }

    /**
     * Retrieve history record.
     *
     * @param int $historyID
     * @param array $with
     *
     * @return History|null
     */
    public function retrieveRecord(int $historyID, array $with = ['changes', 'comments']): ?History
    {
        /** @var History|null $record */
        $record = $this->query->with($with)->where('id', $historyID)->first();

        return $record;
    }

    /**
     * Changes list titles.
     *
     * @return array
     */
    public function getChangesTitles(): array
    {
        return [
            trans('history.history.parameter'),
            trans('history.history.old_value'),
            trans('history.history.new_value'),
        ];
    }
}
