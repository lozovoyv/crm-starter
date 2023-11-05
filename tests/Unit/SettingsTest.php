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

namespace Tests\Unit;

use App\Exceptions\SettingsException;
use App\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AssertLocalizations;

class SettingsTest extends TestCase
{
    use RefreshDatabase, AssertLocalizations;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    /**
     * @throws SettingsException
     */
    public function test_settings(): void
    {
        Settings::set('test', 'test_value');
        Settings::save();
        Settings::reset();
        $this->assertEquals('test_value', Settings::get('test'));
        $this->assertEquals(null, Settings::get('test2'));
    }

    public function test_not_loaded(): void
    {
        $this->expectException(SettingsException::class);
        Settings::reset();
        Settings::save();
    }

    public function test_not_loaded_exception(): void
    {
        $this->assertLocalizations([
            'key' => 'exceptions.system.settings_not_loaded',
        ], function () {
            try {
                Settings::reset();
                Settings::save();
            } catch (SettingsException $exception) {
                return ['key' => $exception->getMessage()];
            }
            return [];
        });
    }
}
