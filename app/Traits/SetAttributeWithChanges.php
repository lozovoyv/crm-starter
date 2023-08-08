<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\History\HistoryChanges;

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
     * @return HistoryChanges|null
     */
    public function setAttributeWithChanges(string $key, mixed $value, ?int $type = null, string $historyKey = null): ?HistoryChanges
    {
        $oldValue = $this->{$key};

        /** @noinspection TypeUnsafeComparisonInspection */
        if ($type !== null && $oldValue != $value) {

            $changes = new HistoryChanges([
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
