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

class DictionaryIndexControllerTest extends TestCase
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
    }

    public function test_dictionary_index_forbidden(): void
    {
        $response = $this->apiActingAs(PositionType::staff, [])->get('/api/dictionaries');

        $this->assertEquals(ApiResponse::CODE_FORBIDDEN, $response->status());
    }

    public function test_dictionary_index(): void
    {
        TestingEloquentDictionary::$editPermissions = [PositionType::staff => ['testing.dictionary']];

        $response = $this->apiActingAs(PositionType::staff, ['system.dictionaries', 'testing.dictionary'])->get('/api/dictionaries');

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());

        $list = $response->json('list');

        $this->assertArrayHasKey('testing', $list);
    }

    public function test_dictionary_index_wo_edit(): void
    {
        TestingEloquentDictionary::$editPermissions = false;
        TestingEloquentDictionary::$viewPermissions = [PositionType::staff => ['testing.dictionary']];

        $response = $this->apiActingAs(PositionType::staff, ['system.dictionaries', 'testing.dictionary'])->get('/api/dictionaries');

        $this->assertEquals(ApiResponse::CODE_OK, $response->status());

        $list = $response->json('list');

        $this->assertArrayNotHasKey('testing', $list);
    }
}
