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

namespace App\HistoryFormatters;

use App\Models\History\HistoryChanges;

interface FormatterInterface
{
    public static function format(HistoryChanges $changes): array;
}
