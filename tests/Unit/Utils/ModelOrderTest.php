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

namespace Tests\Unit\Utils;

use App\Utils\ModelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Assets\Dictionary\TestingEloquentDictionaryModel;
use Tests\TestCase;

class ModelOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_util_model_order(): void
    {
        TestingEloquentDictionaryModel::up();

        for ($i = 0; $i < 5; $i++) {
            $model = new TestingEloquentDictionaryModel();
            $model->id = 10 + $i;
            $model->name = "test_$i";
            $model->save();
        }

        ModelOrder::fix(TestingEloquentDictionaryModel::class);

        $test = TestingEloquentDictionaryModel::query()->select(['id', 'name', 'order'])->get()->toArray();

        $this->assertEquals([
            ['id' => 10, 'name' => 'test_0', 'order' => 0],
            ['id' => 11, 'name' => 'test_1', 'order' => 1],
            ['id' => 12, 'name' => 'test_2', 'order' => 2],
            ['id' => 13, 'name' => 'test_3', 'order' => 3],
            ['id' => 14, 'name' => 'test_4', 'order' => 4],
        ], $test);

        TestingEloquentDictionaryModel::down();
    }

    public function test_util_model_order_no_order(): void
    {
        TestingEloquentDictionaryModel::up();

        for ($i = 0; $i < 5; $i++) {
            $status = new TestingEloquentDictionaryModel();
            $status->id = 10 + $i;
            $status->name = "test_$i";
            $status->save();
        }

        TestingEloquentDictionaryModel::query()->update(['order' => 1]);

        ModelOrder::fix(TestingEloquentDictionaryModel::class, null);

        $testMin = TestingEloquentDictionaryModel::query()->min('order');
        $testMax = TestingEloquentDictionaryModel::query()->max('order');
        $nullCount = TestingEloquentDictionaryModel::query()->whereNull('order')->count();

        $this->assertEquals(1, $testMin);
        $this->assertEquals(1, $testMax);
        $this->assertEquals(0, $nullCount);

        TestingEloquentDictionaryModel::down();
    }
}
