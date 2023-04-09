<?php
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
            switch ($type) {
                case self::int:
                    return $value !== null ? (int)$value : null;
                case self::bool:
                    return $value !== null ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : null;
                case self::datetime:
                    return $value !== null ? Carbon::parse($value) : null;
                case self::array:
                    return !empty($value) ? json_decode($value, true, 512, JSON_THROW_ON_ERROR) : null;
                case self::string:
                default:
            }
        } catch (Exception $exception) {
            throw new CastingException($exception->getMessage(), 0, $exception);
        }

        return $value;
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
            switch ($type) {
                case self::bool:
                    if ($value !== null) {
                        $value = $value ? '1' : '0';
                    }
                    break;
                case self::array:
                    $value = !empty($value) ? json_encode($value, JSON_THROW_ON_ERROR) : null;
                    break;
                case self::int:
                case self::datetime:
                case self::string:
                default:
                    $value = $value !== null ? (string)$value : null;
                    break;
            }
        } catch (Exception $exception) {
            throw new CastingException($exception->getMessage(), 0, $exception);
        }

        return $value;
    }
}
