<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Actions;

use App\Current;

class Action
{
    protected ?Current $current;

    public function __construct(?Current $current = null)
    {
        $this->current = $current;
    }
}
