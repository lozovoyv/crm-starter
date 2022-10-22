<?php

namespace App\Foundation;

use Carbon\Carbon;
use JsonException;

class Casting
{
    public const string = 0;
    public const int = 1;
    public const bool = 2;
    public const datetime = 3;
    public const array = 4;

    /**
     * Cast value to type.
     *
     * @param string|null $value
     * @param int $type
     *
     * @return bool|int|string|array|Carbon|null
     * @throws JsonException
     */
    public static function castTo(?string $value, int $type): bool|int|string|array|Carbon|null
    {
        switch ($type) {
            case self::int:
                return $value !== null ? (int)$value : null;
            case self::bool:
                return $value !== null ? (bool)$value : null;
            case self::datetime:
                return $value !== null ? Carbon::parse($value) : null;
            case self::array:
                return !empty($value) ? json_decode($value, true, 512, JSON_THROW_ON_ERROR) : null;
            case self::string:
            default:
        }

        return $value;
    }

    /**
     * Cast value from type.
     *
     * @param bool|int|string|null $value
     * @param int $type
     *
     * @return  string|null
     * @throws JsonException
     */
    public static function castFrom(mixed $value, int $type): ?string
    {
        switch ($type) {
            case self::int:
            case self::bool:
            case self::datetime:
                $value = $value !== null ? (string)$value : null;
                break;
            case self::array:
                $value = !empty($value) ? json_encode($value, JSON_THROW_ON_ERROR) : null;
                break;
            case self::string:
            default:
        }

        return $value;
    }
}
