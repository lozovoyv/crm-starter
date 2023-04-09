<?php
declare(strict_types=1);

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
     *
     * @return  History
     */
    public function addHistory(int $action, string $scope, ?int $scopedId, ?string $description = null, ?int $operatorId = null): History
    {
        $record = new History;
        $record->history_line_id = $this->id;
        $record->action_id = $action;
        $record->entry_name = $scope;
        $record->entry_id = $scopedId;
        $record->description = $description;
        $record->timestamp = Carbon::now();
        $record->position_id = $operatorId;
        $record->save();

        return $record;
    }
}
