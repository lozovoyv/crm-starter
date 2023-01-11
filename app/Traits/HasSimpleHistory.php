<?php

namespace App\Traits;

use App\Models\History\History;
use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSimpleHistory
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
     * Related history.
     *
     * @return  MorphMany
     */
    public function entries(): MorphMany
    {
        return $this->morphMany(History::class, 'entry');
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
