<?php

namespace App;

use App\Foundation\Casting;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class Settings
{
    /** @var string[] System store. */
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
    public static function get(string $key, mixed $default = null, int $type = Casting::string): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        return (self::$settings && array_key_exists($key, self::$settings)) ? Casting::castTo(self::$settings[$key], $type) : $default;
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
    public static function set(string $key, mixed $value, int $type = Casting::string): void
    {
        if (!self::$loaded) {
            self::load();
        }

        self::$settings[$key] = Casting::castFrom($value, $type);
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
            throw new RuntimeException('System must be loaded before saving.');
        }
        $values = [];
        foreach (self::$settings as $key => $value) {
            $values[] = ['key' => $key, 'value' => $value];
        }
        DB::table('settings')->truncate();
        DB::table('settings')->insert($values);
    }
}
