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

namespace App\Actions;

use App\Current;

abstract class Action
{
    protected ?Current $current;

    protected ?string $resultMessage = null;

    public function __construct(?Current $current = null)
    {
        $this->current = $current;
    }

    /**
     * Get result text representation.
     *
     * @return string|null
     */
    public function getResultMessage(): ?string
    {
        return !empty($this->resultMessage) ? trans($this->resultMessage) : null;
    }
}
