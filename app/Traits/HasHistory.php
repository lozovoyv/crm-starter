<?php

namespace App\Traits;

use App\Models\History\History;
use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasHistory
{
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
        /** @var History $record */
        $record = $this->history()->create([
            'action_id' => $actionId,
            'history_line_id' => $this->history_line_id ?? null,
            'entry_name' => $this->historyEntryName(),
            'entry_id' => $this->id,
            'description' => $description,
            'position_id' => $positionId,
            'timestamp' => Carbon::now(),
        ]);

        return $record;
    }
}
