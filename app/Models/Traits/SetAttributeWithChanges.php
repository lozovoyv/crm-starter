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

use App\Models\History\HistoryChange;

trait SetAttributeWithChanges
{
    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $type
     * @param string|null $historyKey
     *
     * @return HistoryChange|null
     */
    public function setAttributeWithChanges(string $key, mixed $value, ?int $type = null, string $historyKey = null): ?HistoryChange
    {
        $oldValue = $this->{$key};

        /** @noinspection TypeUnsafeComparisonInspection */
        if ($type !== null && $oldValue != $value) {

            $changes = new HistoryChange([
                'parameter' => $historyKey ?? $key,
                'type' => $type,
                'old' => $this->exists ? $oldValue : null,
                'new' => $value,
            ]);
        }

        $this->{$key} = $value;

        return $changes ?? null;
    }
}
