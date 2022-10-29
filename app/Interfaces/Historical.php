<?php

namespace App\Interfaces;

use App\Models\History\History;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface Historical
{
    /**
     * History entry title.
     *
     * @return  string
     */
    public function historyEntryTitle(): string;

    /**
     * History entry name.
     *
     * @return  string
     */
    public function historyEntryName(): string;

    /**
     * History entry type.
     *
     * @return  string|null
     */
    public function historyEntryType(): ?string;

    /**
     * Related history.
     *
     * @return  HasMany
     */
    public function history(): HasMany;

    /**
     * Add record to history.
     *
     * @param int $actionId
     * @param string|null $description
     * @param int|null $positionId
     *
     * @return  History
     */
    public function addHistory(int $actionId, ?int $positionId, ?string $description = null): History;
}
