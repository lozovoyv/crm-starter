<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

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
        $this->expectException(RuntimeException::class);
        Settings::reset();
        Settings::save();
    }
}
