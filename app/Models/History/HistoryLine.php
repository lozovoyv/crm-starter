<?php

namespace App\Models\History;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $records
 */
class HistoryLine extends Model
{
    /** @var string[] Attribute casting. */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * History records.
     *
     * @return  HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(History::class, 'history_id', 'id')->orderBy('timestamp', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Add record to history.
     *
     * @param int $action
     * @param string $scope
     * @param int|null $scopedId
     * @param string|null $description
     * @param int|null $operatorId
     * @param int|null $operandId
     * @param array|null $changes
     * @param array|null $links
     *
     * @return  History
     */
    public function addHistory(int $action, string $scope, ?int $scopedId, ?string $description = null, ?int $operatorId = null, ?int $operandId = null, ?array $changes = null, ?array $links = null): History
    {
        $record = new History;
        $record->history_id = $this->id;
        $record->action_id = $action;
        $record->scope = $scope;
        $record->scoped_id = $scopedId;
        $record->description = $description;
        $record->timestamp = Carbon::now();
        $record->operator_id = $operatorId;
        $record->operand_id = $operandId;
        $record->changed = $changes;
        $record->links = $links;
        $record->save();

        return $record;
    }
}
