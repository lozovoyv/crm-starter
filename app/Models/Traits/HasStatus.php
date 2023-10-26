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

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasStatus
{
    /**
     * Check and set new status for the model.
     *
     * @param string $class
     * @param int|string $id
     * @param string $field
     *
     * @param bool $save
     *
     * @return  void
     */
    protected function checkAndSetStatus(string $class, int|string $id, string $field = 'status_id', bool $save = false): void
    {
        /** @var Model $class */
        $statusEntry = $class::query()->find($id);

        if ($statusEntry === null || !$statusEntry->exists) {
            throw new InvalidArgumentException('Неверный статус.');
        }

        $this->setAttribute($field, $id);

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
