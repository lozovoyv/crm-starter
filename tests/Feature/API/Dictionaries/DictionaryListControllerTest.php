<?php
declare(strict_types=1);

namespace Tests\Feature\API\Dictionaries;

use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use App\Models\Positions\PositionType;
use App\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Assets\Dictionary\TestingEloquentDictionary;
use Tests\Assets\Dictionary\TestingEloquentDictionaryModel;
use Tests\TestCase;

class DictionaryListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();

        if (!Permission::query()->where('key', 'testing.dictionary')->exists()) {
            if (!PermissionScope::query()->where('scope_name', 'dictionary')->exists()) {
                PermissionScope::query()->create([
                    'scope_name' => 'dictionary',
                    'name' => 'dictionary testing',
                ]);
            }
            !Permission::query()->create([
                'key' => 'testing.dictionary',
                'scope_name' => 'dictionary',
                'name' => 'dictionary testing permission',
            ]);
        }

        config()->set('dictionaries.testing', TestingEloquentDictionary::class);
        TestingEloquentDictionaryModel::up();
        TestingEloquentDictionaryModel::query()->create(['name' => 'test', 'order' => 2]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 2', 'order' => 1]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 3', 'enabled' => false]);
    }

    public function test_dictionary_list_editor_forbidden(): void
    {
        $response = $this->apiActingAs(PositionType::staff, [])->get('/api/dictionaries/testing');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }

    public function test_dictionary_list_dictionary_forbidden(): void
    {
        TestingEloquentDictionary::$editPermissions = false;

        $response = $this->apiActingAs(PositionType::staff, [Permissions::system__dictionaries])->get('/api/dictionaries/testing');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }

    public function test_dictionary_list_dictionary_success(): void
    {
        TestingEloquentDictionary::$editPermissions = [PositionType::staff => ['testing.dictionary']];

        $response = $this->apiActingAs(PositionType::staff, [Permissions::system__dictionaries, 'testing.dictionary'])->get('/api/dictionaries/testing');

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
    }

    public function test_dictionary_list_dictionary_content(): void
    {
        TestingEloquentDictionary::$editPermissions = [PositionType::staff => ['testing.dictionary']];

        $response = $this->apiActingAs(PositionType::staff, [Permissions::system__dictionaries, 'testing.dictionary'])->get('/api/dictionaries/testing');

        $content = $response->json();

        $this->assertEquals('Абстрактный справочник', $content['title']);
        $this->assertEquals([
            'name' => 'Name',
            'description' => 'Description',
        ], $content['titles']);

        $payload = $content['payload'];
        $this->assertTrue($payload['orderable']);
        $this->assertTrue($payload['switchable']);
        $this->assertEquals([
            'name' => 'string',
            'description' => 'string',
        ], $payload['types']);

        $list = array_map(function ($item) {
            unset($item['updated_at']);
            return $item;
        }, $content['list']);

        $this->assertEquals([
            [
                'id' => 3, 'enabled' => false, 'order' => 0, 'locked' => false, 'name' => 'test 3', 'description' => null, 'hash' => 'test hash',
            ], [
                'id' => 2, 'enabled' => true, 'order' => 1, 'locked' => false, 'name' => 'test 2', 'description' => null, 'hash' => 'test hash',
            ], [
                'id' => 1, 'enabled' => true, 'order' => 2, 'locked' => false, 'name' => 'test', 'description' => null, 'hash' => 'test hash',
            ],
        ], $list);
    }

}
