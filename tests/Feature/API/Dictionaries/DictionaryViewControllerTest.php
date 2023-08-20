<?php
declare(strict_types=1);

namespace Tests\Feature\API\Dictionaries;

use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use App\Models\Positions\PositionType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Assets\Dictionary\TestingEloquentDictionary;
use Tests\Assets\Dictionary\TestingEloquentDictionaryModel;
use Tests\TestCase;

class DictionaryViewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();

        TestingEloquentDictionaryModel::up();
        TestingEloquentDictionaryModel::query()->create(['name' => 'test', 'order' => 2]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 2', 'order' => 1]);
        TestingEloquentDictionaryModel::query()->create(['name' => 'test 3', 'enabled' => false]);

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
    }

    public function test_dictionary_controller_not_found(): void
    {
        $response = $this->apiActingAs(PositionType::staff, ['testing.dictionary'])->get('/api/dictionary/fake_test');

        $this->assertEquals(ApiResponse::CODE_NOT_FOUND, $response->status());
    }

    public function test_dictionary_controller_forbidden(): void
    {
        TestingEloquentDictionary::$viewPermissions = false;
        TestingEloquentDictionary::$editPermissions = false;

        $response = $this->apiActingAs(PositionType::staff, ['testing.dictionary'])->get('/api/dictionary/testing');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }

    public function test_dictionary_controller_full_assess(): void
    {
        $this->apiActingAs(PositionType::staff, ['testing.dictionary']);

        TestingEloquentDictionary::$viewPermissions = [PositionType::staff => ['testing.dictionary']];
        $response = $this->get('/api/dictionary/testing');
        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
        $response->assertJson([
            'list' => [
                ['id' => 3, 'name' => "test 3", 'hint' => null, 'enabled' => false, 'order' => 0],
                ['id' => 2, 'name' => "test 2", 'hint' => null, 'enabled' => true, 'order' => 1],
                ['id' => 1, 'name' => "test", 'hint' => null, 'enabled' => true, 'order' => 2],
            ],
            'payload' => [
                'is_editable' => false,
            ],
        ]);

        TestingEloquentDictionary::$editPermissions = [PositionType::staff => ['testing.dictionary']];
        $response = $this->get('/api/dictionary/testing');
        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
        $response->assertJson([
            'list' => [
                ['id' => 3, 'name' => "test 3", 'hint' => null, 'enabled' => false, 'order' => 0],
                ['id' => 2, 'name' => "test 2", 'hint' => null, 'enabled' => true, 'order' => 1],
                ['id' => 1, 'name' => "test", 'hint' => null, 'enabled' => true, 'order' => 2],
            ],
            'payload' => [
                'is_editable' => true,
            ],
        ]);
    }

    public function test_dictionary_controller_modified_since(): void
    {
        $this->apiActingAs(PositionType::staff, ['testing.dictionary']);
        TestingEloquentDictionary::$viewPermissions = true;
        TestingEloquentDictionary::$editPermissions = false;

        $response = $this
            ->withHeader('if-modified-since', Carbon::now()->addDays(-1)->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT')
            ->get('/api/dictionary/testing');
        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
        $response->assertJson([
            'list' => [
                ['id' => 3, 'name' => "test 3", 'hint' => null, 'enabled' => false, 'order' => 0],
                ['id' => 2, 'name' => "test 2", 'hint' => null, 'enabled' => true, 'order' => 1],
                ['id' => 1, 'name' => "test", 'hint' => null, 'enabled' => true, 'order' => 2],
            ],
            'payload' => [
                'is_editable' => false,
            ],
        ]);
    }

    public function test_dictionary_controller_not_modified_since(): void
    {
        $this->apiActingAs(PositionType::staff, ['testing.dictionary']);
        TestingEloquentDictionary::$viewPermissions = true;
        TestingEloquentDictionary::$editPermissions = false;

        $response = $this
            ->withHeader('if-modified-since', Carbon::now()->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT')
            ->get('/api/dictionary/testing');
        $response->assertNoContent(ApiResponse::CODE_NOT_MODIFIED);
    }

    public function test_dictionary_controller_not_modified_force_update(): void
    {
        $this->apiActingAs(PositionType::staff, ['testing.dictionary']);
        TestingEloquentDictionary::$viewPermissions = true;
        TestingEloquentDictionary::$editPermissions = false;

        $response = $this
            ->withHeader('if-modified-since', Carbon::now()->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT')
            ->withHeader('x-force-update', 'true')
            ->get('/api/dictionary/testing');
        $this->assertEquals(ApiResponse::CODE_OK, $response->status());
        $response->assertJson([
            'list' => [
                ['id' => 3, 'name' => "test 3", 'hint' => null, 'enabled' => false, 'order' => 0],
                ['id' => 2, 'name' => "test 2", 'hint' => null, 'enabled' => true, 'order' => 1],
                ['id' => 1, 'name' => "test", 'hint' => null, 'enabled' => true, 'order' => 2],
            ],
            'payload' => [
                'is_editable' => false,
            ],
        ]);
    }
}
