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

namespace App;

use App\Exceptions\CastingException;
use App\Exceptions\SettingsException;
use App\Utils\Casting;
use Illuminate\Support\Facades\DB;
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
     *
     * @throws CastingException
     */
    public static function get(string $key, mixed $default = null, int $type = Casting::string): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        return (self::$settings && array_key_exists($key, self::$settings)) ? Casting::fromString(self::$settings[$key], $type) : $default;
    }

    /**
     * Set value to settings.
     *
     * @param string $key
     * @param mixed $value
     * @param int $type
     *
     * @return  void
     *
     * @throws CastingException
     */
    public static function set(string $key, mixed $value, int $type = Casting::string): void
    {
        if (!self::$loaded) {
            self::load();
        }

        self::$settings[$key] = Casting::toString($value, $type);
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
     *
     * @throws SettingsException
     */
    public static function save(): void
    {
        if (self::$loaded === false) {
            throw new SettingsException('exceptions.system.settings_not_loaded');
        }
        $values = [];
        foreach (self::$settings as $key => $value) {
            $values[] = ['key' => $key, 'value' => $value];
        }
        DB::table('settings')->truncate();
        if (!empty($values)) {
            DB::table('settings')->insert($values);
        }
    }

    /**
     * Reset settings to initial state.
     *
     * @return  void
     */
    public static function reset(): void
    {
        self::$loaded = false;
        self::$settings = [];
    }
}
