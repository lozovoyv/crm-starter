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

interface HashCheckable
{
    /**
     * Make hash to check modifications.
     *
     * @return  string|null
     */
    public function hash(): ?string;

    /**
     * Check hash against given.
     *
     * @param string|null $hash
     *
     * @return  bool
     */
    public function isHash(?string $hash): bool;

    /**
     * Make hash for model.
     *
     * @return  string|null
     */
    public function hashable(): ?string;
}
