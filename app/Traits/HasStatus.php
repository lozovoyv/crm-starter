<?php

namespace App\Traits;

use App\Models\Dictionaries\AbstractDictionary;

trait HasStatus
{
    /**
     * Check and set new status for the model having `status_id` attribute.
     *
     * @param string $class
     * @param int $status
     * @param string $exception
     * @param bool $save
     * @param string $name
     *
     * @return  void
     *
     */
    protected function checkAndSetStatus(string $class, int $status, string $exception, bool $save = false, string $name = 'status_id'): void
    {
        /** @var AbstractDictionary $class */
        $statusEntry = $class::get($status);

        if ($statusEntry === null || !$statusEntry->exists) {
            throw new $exception;
        }

        $this->setAttribute($name, $statusEntry->id);

        if ($save) {
            $this->save();
        }
    }

    /**
     * Check if the model has the status or one of statuses.
     *
     * @param int $status
     *
     * @return  bool
     */
    public function hasStatus(int $status): bool
    {
        if(is_array($status)) {
            return in_array($this->getAttribute($name), $status, true);
        }

        return $this->getAttribute($name) === $status;
    }
}
