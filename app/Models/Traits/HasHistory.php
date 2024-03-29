<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\History\History;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<History> $history
 */
trait HasHistory
{
    /**
     * Related history.
     *
     * @return  MorphMany
     */
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'entry');
    }

    /**
     * History entry caption.
     *
     * @return string|null
     */
    public function historyEntryCaption(): ?string
    {
        return null;
    }

    /**
     * History entry tag.
     *
     * @return string|null
     */
    public function historyEntryTag(): ?string
    {
        return null;
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
        $record = History::query()->create([
            'action_id' => $actionId,
            'entry_type' => __CLASS__,
            'entry_id' => $this->id,
            'entry_caption' => $this->historyEntryCaption(),
            'entry_tag' => $this->historyEntryTag(),
            'description' => $description,
            'position_id' => $positionId,
            'timestamp' => Carbon::now(),
        ]);

        return $record;
    }
}
