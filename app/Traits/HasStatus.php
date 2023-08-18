<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasStatus
{
    /**
     * Check and set new status for the model.
     *
     * @param string $class
     * @param int $status_id
     * @param bool $save
     * @param string $name
     *
     * @return  void
     */
    protected function checkAndSetStatus(string $class, int $status_id, string $name = 'status_id', bool $save = false): void
    {
        /** @var Model $class */
        $statusEntry = $class::query()->find($status_id);
        // todo check enabled ??? vis dictionaries ????
        if ($statusEntry === null || !$statusEntry->exists) {
            throw new InvalidArgumentException('Неверный статус.');
        }

        $this->setAttribute($name, $status_id);

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
