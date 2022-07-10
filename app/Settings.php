<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class Settings
{
    public const string = 0;
    public const int = 1;
    public const bool = 2;
    public const datetime = 3;
    public const array = 4;

    /** @var string[] Settings store. */
    protected static array $settings;

    /** @var bool Loaded state. */
    protected static bool $loaded = false;

    /**
     * Get value from settings.
     *
     * @param string $key
     * @param mixed|null $default
     * @param int $type
     *
     * @return  mixed
     * @throws JsonException
     */
    public static function get(string $key, mixed $default = null, int $type = Settings::string): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        return (self::$settings && array_key_exists($key, self::$settings)) ? self::castTo(self::$settings[$key], $type) : $default;
    }

    /**
     * Set value to settings.
     *
     * @param string $key
     * @param mixed $value
     * @param int $type
     *
     * @return  void
     * @throws JsonException
     */
    public static function set(string $key, mixed $value, int $type = Settings::string): void
    {
        if (!self::$loaded) {
            self::load();
        }

        self::$settings[$key] = self::castFrom($value, $type);
    }

    /**
     * Load settings.
     *
     * @return  void
     */
    public static function load(): void
    {
        $settings = DB::table('settings')->get();

        self::$settings = [];

        foreach ($settings as $setting) {
            self::$settings[$setting->key] = $setting->value;
        }

        self::$loaded = true;
    }

    /**
     * Save settings.
     *
     * @return  void
     */
    public static function save(): void
    {
        if (self::$loaded === false) {
            throw new RuntimeException('Settings must be loaded before saving.');
        }
        $values = [];
        foreach (self::$settings as $key => $value) {
            $values[] = ['key' => $key, 'value' => $value];
        }
        DB::table('settings')->truncate();
        DB::table('settings')->insert($values);
    }

    /**
     * Cast value to type.
     *
     * @param string|null $value
     * @param int $type
     *
     * @return bool|int|string|array|Carbon|null
     * @throws JsonException
     */
    protected static function castTo(?string $value, int $type): bool|int|string|array|Carbon|null
    {
        switch ($type) {
            case self::int:
                return $value !== null ? (int)$value : null;
            case self::bool:
                return (bool)$value;
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
    protected static function castFrom(mixed $value, int $type): ?string
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
