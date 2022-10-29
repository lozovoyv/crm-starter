<?php

namespace App\Traits;

use App\Models\History\History;
use App\Models\History\HistoryLine;
use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasHistoryLine
{
    /**
     * Related history line.
     *
     * @return  BelongsTo
     */
    public function historyLine(): BelongsTo
    {
        return $this->belongsTo(HistoryLine::class, 'history_line_id')->withDefault();
    }

    /**
     * Related history.
     *
     * @return  HasMany
     */
    public function history(): HasMany
    {
        if (!method_exists($this, 'historyEntryName')) {
            throw new BadMethodCallException('Undefined `historyEntryName()` method on ' . __CLASS__);
        }

        return $this->hasMany(History::class, 'entry_id', 'id')->where('entry_name', $this->historyEntryName());
    }

    /**
     * Add record to history.
     *
     * @param int $actionId
     * @param string|null $description
     * @param int|null $positionId
     *
     * @return  History
     */
    public function addHistory(int $actionId, ?int $positionId, ?string $description = null): History
    {
        $this->loadMissing('historyLine');
        $historyLine = $this->getRelation('historyLine');

        if (!$historyLine->exists) {
            $historyLine->save();
            $this->history_line_id = $historyLine->id;
            $this->save();
        }

        /** @var History $record */
        $record = $this->history()->create([
            'history_line_id' => $this->history_line_id,
            'action_id' => $actionId,
            'entry_title' => $this->historyEntryTitle(),
            'entry_name' => $this->historyEntryName(),
            'entry_type' => $this->historyEntryType(),
            'entry_id' => $this->id,
            'description' => $description,
            'position_id' => $positionId,
            'timestamp' => Carbon::now(),
        ]);

        return $record;
    }
}
