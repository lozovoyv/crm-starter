<?php
declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Models\Users\UserStatus;
use App\Utils\ModelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ModelOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_util_model_order(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $status = new UserStatus();
            $status->id = 10 + $i;
            $status->name = "test_$i";
            $status->save();
        }

        ModelOrder::fix(UserStatus::class, 'order');

        $test = UserStatus::query()->select(['id', 'name', 'order'])->get()->toArray();

        $this->assertEquals([
            ['id' => 10, 'name' => 'test_0', 'order' => 0],
            ['id' => 11, 'name' => 'test_1', 'order' => 1],
            ['id' => 12, 'name' => 'test_2', 'order' => 2],
            ['id' => 13, 'name' => 'test_3', 'order' => 3],
            ['id' => 14, 'name' => 'test_4', 'order' => 4],
        ], $test);
    }

    public function test_util_model_order_no_order(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $status = new UserStatus();
            $status->id = 10 + $i;
            $status->name = "test_$i";
            $status->save();
        }

        UserStatus::query()->update(['order' => 1]);

        ModelOrder::fix(UserStatus::class, null);

        $testMin = UserStatus::query()->min('order');
        $testMax = UserStatus::query()->max('order');
        $nullCount = UserStatus::query()->whereNull('order')->count();

        $this->assertEquals(1, $testMin);
        $this->assertEquals(1, $testMax);
        $this->assertEquals(0, $nullCount);
    }
}
