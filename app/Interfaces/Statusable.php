<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasOne;
use InvalidArgumentException;

interface Statusable
{
    /**
     * Status of the model.
     *
     * @return  HasOne
     */
    public function status(): HasOne;

    /**
     * Check and set new status for model.
     *
     * @param int $status
     * @param bool $save
     *
     * @return  void
     *
     * @throws InvalidArgumentException
     */
    public function setStatus(int $status, bool $save = false): void;

    /**
     * Check if the model has the status.
     *
     * @param int $status
     *
     * @return bool
     */
    public function hasStatus(int $status): bool;
}
