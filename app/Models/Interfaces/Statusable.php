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
