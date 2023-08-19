<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class DictionaryEloquentTest extends TestCase
{
    public function test_dictionary_eloquent_query(): void
    {
        TestingEloquentDictionaryModel::up();

        TestingEloquentDictionaryModel::query()->create(['name' => 'test', 'order' => 2, 'enabled' => true]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 2', 'order' => 1, 'enabled' => true]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 3', 'order' => 0, 'enabled' => false]);

        $query = TestingEloquentDictionary::query();
        $result = $query->get();

        $this->assertEquals(3, $result->count());
        $this->assertEquals([
            [
                'id' => 3, 'name' => 'test 3', 'hint' => null, 'enabled' => false, 'order' => 0, 'updated_at' => null,
            ],
            [
                'id' => 2, 'name' => 'test 2', 'hint' => null, 'enabled' => true, 'order' => 1, 'updated_at' => null,
            ],
            [
                'id' => 1, 'name' => 'test', 'hint' => null, 'enabled' => true, 'order' => 2, 'updated_at' => null,
            ],
        ], $result->map(function (TestingEloquentDictionaryModel $dict) {
            $dict->updated_at = null;
            return $dict;
        })->toArray());

        TestingEloquentDictionaryModel::down();
    }

    public function test_dictionary_eloquent_list_query(): void
    {
        TestingEloquentDictionaryModel::up();

        TestingEloquentDictionaryModel::query()->create(['name' => 'test', 'order' => 2]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 2', 'order' => 1]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 3', 'enabled' => false]);

        $query = TestingEloquentDictionary::listQuery();
        $result = $query->get();

        $this->assertEquals(3, $result->count());
        $this->assertEquals([
            [
                'id' => 3, 'name' => 'test 3', 'enabled' => false, 'order' => 0, 'updated_at' => null, 'locked' => false, 'description' => null,
            ],
            [
                'id' => 2, 'name' => 'test 2', 'enabled' => true, 'order' => 1, 'updated_at' => null, 'locked' => false, 'description' => null,
            ],
            [
                'id' => 1, 'name' => 'test', 'enabled' => true, 'order' => 2, 'updated_at' => null, 'locked' => false, 'description' => null,
            ],
        ], $result->map(function (TestingEloquentDictionaryModel $dict) {
            $dict->updated_at = null;
            return $dict;
        })->toArray());

        TestingEloquentDictionaryModel::down();
    }
}
