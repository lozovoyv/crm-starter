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

namespace App\Utils;

class Translate
{
    public static function array(array $keys): array
    {
        return array_map(static function (?string $key): ?string {
            return $key ? trans($key) : null;
        }, $keys);
    }
}
