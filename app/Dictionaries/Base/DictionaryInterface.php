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

namespace App\Dictionaries\Base;

use App\Current;
use Carbon\Carbon;

interface DictionaryInterface
{
    public static function view(Current $current, ?Carbon $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewContainerInterface;
}
