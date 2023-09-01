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

class Name
{
    /**
     * Format part of name. Make firs letter uppercase and next dot or lowercase letters.
     *
     * @param string|null $value
     * @param bool $compact
     *
     * @return string|null
     */
    public static function part(?string $value, bool $compact = false): ?string
    {
        if (empty($value)) {
            return null;
        }

        return mb_strtoupper(mb_substr($value, 0, 1)) . ($compact ? '.' : mb_strtolower(mb_substr($value, 1)));
    }

    /**
     * Format to full name.
     *
     * @param string|null $lastname
     * @param string|null $firstname
     * @param string|null $patronymic
     *
     * @return string|null
     */
    public static function full(?string $lastname, ?string $firstname, ?string $patronymic): ?string
    {
        if (empty($lastname) && empty($firstname) && empty($patronymic)) {
            return null;
        }

        return trim(implode(' ', [self::part($lastname), self::part($firstname), self::part($patronymic)]));
    }

    /**
     * Format to compact name.
     *
     * @param string|null $lastname
     * @param string|null $firstname
     * @param string|null $patronymic
     *
     * @return string|null
     */
    public static function compact(?string $lastname, ?string $firstname, ?string $patronymic): ?string
    {
        if (empty($lastname) && empty($firstname) && empty($patronymic)) {
            return null;
        }

        return trim(sprintf('%s %s%s', self::part($lastname), self::part($firstname, true), self::part($patronymic, true)));
    }
}
