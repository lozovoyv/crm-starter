<?php

namespace App\Traits;

use App\Foundation\Dictionaries\AbstractDictionary;
use InvalidArgumentException;

trait HasStatus
{
    /**
     * Check and set new status for the model.
     *
     * @param string $class
     * @param int $status
     * @param bool $save
     * @param string $name
     *
     * @return  void
     */
    protected function checkAndSetStatus(string $class, int $status, string $name = 'status_id', bool $save = false): void
    {
        /** @var AbstractDictionary $class */
        $statusEntry = $class::get($status);

        if ($statusEntry === null || !$statusEntry->exists) {
            throw new InvalidArgumentException('Неверный статус.');
        }

        $this->setAttribute($name, $status);

        if ($save) {
            $this->save();
        }
    }

    /**
     * Check if the model has the status or one of statuses.
     *
     * @param int|array $status
     * @param string $name
     *
     * @return  bool
     */
    public function hasStatus($status, string $name = 'status_id'): bool
    {
        if(is_array($status)) {
            return in_array($this->getAttribute($name), $status, true);
        }

        return $this->getAttribute($name) === $status;
    }
}
