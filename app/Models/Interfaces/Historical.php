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

namespace App\Models\Interfaces;

use App\Models\History\History;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Historical
{
    /**
     * History entry caption.
     *
     * @return string|null
     */
    public function historyEntryCaption(): ?string;

    /**
     * History entry tag.
     *
     * @return string|null
     */
    public function historyEntryTag(): ?string;

    /**
     * Related history.
     *
     * @return MorphMany
     */
    public function history(): MorphMany;

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
