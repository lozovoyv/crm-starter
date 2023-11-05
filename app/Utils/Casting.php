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

namespace App\Utils;

use App\Exceptions\CastingException;
use Carbon\Carbon;
use Exception;

class Casting
{
    public const string = 0;
    public const int = 1;
    public const bool = 2;
    public const datetime = 3;
    public const array = 4;

    public const type_string = 'string';
    public const type_int = 'int';
    public const type_bool = 'bool';
    public const type_datetime = 'datetime';
    public const type_array = 'array';

    /**
     * Convert string definition to constant,
     *
     * @param string|null $type
     *
     * @return int
     */
    public static function type(?string $type): int
    {
        return match ($type ?? null) {
            self::type_int => self::int,
            self::type_bool => self::bool,
            self::type_datetime => self::datetime,
            self::type_array => self::array,
            default => self::string,
        };
    }

    /**
     * Cast value to type.
     *
     * @param string|null $value
     * @param int|string|null $type
     *
     * @return bool|int|string|array|Carbon|null
     * @throws CastingException
     */
    public static function fromString(?string $value, int|string|null $type): bool|int|string|array|Carbon|null
    {
        if (!is_int($type)) {
            $type = self::type($type);
        }

        try {
            $casted = match ($type) {
                self::int => $value !== null ? (int)$value : null,
                self::bool => $value !== null && $value !== '' ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : null,
                self::datetime => $value !== null ? Carbon::parse($value) : null,
                self::array => !empty($value) ? json_decode($value, true, 512, JSON_THROW_ON_ERROR) : null,
                default => !empty($value) ? $value : null,
            };
        } catch (Exception $exception) {
            throw new CastingException($exception->getMessage(), 0, $exception);
        }

        return $casted;
    }

    /**
     * Cast value from type.
     *
     * @param bool|int|string|null $value
     * @param int|string|null $type
     *
     * @return  string|null
     * @throws CastingException
     */
    public static function toString(mixed $value, int|string|null $type): ?string
    {
        if (!is_int($type)) {
            $type = self::type($type);
        }

        try {
            $casted = match ($type) {
                self::bool => match ($value) {
                    null => null,
                    default => ($value ? '1' : '0'),
                },
                self::array => !empty($value) ? json_encode($value, JSON_THROW_ON_ERROR) : null,
                default => $value !== null && $value !== '' ? (string)$value : null,
            };
        } catch (Exception $exception) {
            throw new CastingException($exception->getMessage(), 0, $exception);
        }

        return $casted;
    }

    /**
     * Cast filters from string.
     *
     * @param array|null $array
     * @param array $casting
     *
     * @return array
     */
    public static function castArray(?array $array, array $casting = []): array
    {
        if (empty($array)) {
            $array = [];
        }

        foreach ($casting as $key => $type) {
            if (isset($array[$key])) {
                $array[$key] = self::fromString($array[$key], $type);
            } else {
                $array[$key] = null;
            }
        }
        return $array;
    }
}
