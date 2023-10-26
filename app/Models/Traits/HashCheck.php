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

use Carbon\Carbon;

/**
 * @property Carbon|null $updated_at
 */
trait HashCheck
{
    /**
     * Get value to hash.
     *
     * @return  string|null
     */
    public function hashable(): ?string
    {
        if (empty($this->updated_at)) {
            return null;
        }

        return $this->updated_at instanceof Carbon ? $this->updated_at->toString() : (string)$this->updated_at;
    }

    /**
     * Make hash to check modifications.
     *
     * @return string|null
     */
    public function hash(): ?string
    {
        $hash = $this->hashable();

        return $hash ? md5($this->hashable()) : null;
    }

    /**
     * Check hash against given.
     *
     * @param string|null $hash
     *
     * @return bool
     */
    public function isHash(?string $hash): bool
    {
        return $this->hash() === $hash;
    }
}
