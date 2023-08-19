<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\HelperTraits\CreatesCurrent;
use Tests\TestCase;

class DictionaryBaseTest extends TestCase
{
    use CreatesCurrent, RefreshDatabase;

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
    }

    public function test_dictionary_titles_and_messages(): void
    {
        App::setLocale('en');
        $this->assertEquals('Dictionary test not found', TestingDictionary::messageDictionaryNotFound('test'));
        $this->assertEquals('Dictionary test prohibited', TestingDictionary::messageDictionaryForbidden('test'));
        $this->assertEquals('Abstract dictionary', TestingDictionary::title());
        $this->assertEquals('Create item', TestingDictionary::titleFormCreate());
        $this->assertEquals('Edit item test', TestingDictionary::titleFormEdit('test'));
        $this->assertEquals('Item test created', TestingDictionary::messageItemCreatedSuccessfully('test'));
        $this->assertEquals('Item test saved', TestingDictionary::messageItemEditedSuccessfully('test'));
        $this->assertEquals('Item test disabled', TestingDictionary::messageItemDisabled('test'));
        $this->assertEquals('Item test enabled', TestingDictionary::messageItemEnabled('test'));
        $this->assertEquals('Item test deleted', TestingDictionary::messageItemDeleted('test'));
        $this->assertEquals('Item not found', TestingDictionary::messageItemNotFound());
        $this->assertEquals('Order of items changed', TestingDictionary::messageItemsReordered());

        App::setLocale('ru');
        $this->assertEquals('Справочник test не найден', TestingDictionary::messageDictionaryNotFound('test'));
        $this->assertEquals('Доступ к справочнику test запрещен', TestingDictionary::messageDictionaryForbidden('test'));
        $this->assertEquals('Абстрактный справочник', TestingDictionary::title());
        $this->assertEquals('Добавление записи', TestingDictionary::titleFormCreate());
        $this->assertEquals('Редактирование записи test', TestingDictionary::titleFormEdit('test'));
        $this->assertEquals('Запись test добавлена', TestingDictionary::messageItemCreatedSuccessfully('test'));
        $this->assertEquals('Запись test сохранена', TestingDictionary::messageItemEditedSuccessfully('test'));
        $this->assertEquals('Запись test отключена', TestingDictionary::messageItemDisabled('test'));
        $this->assertEquals('Запись test включена', TestingDictionary::messageItemEnabled('test'));
        $this->assertEquals('Запись test удалена', TestingDictionary::messageItemDeleted('test'));
        $this->assertEquals('Запись не найдена', TestingDictionary::messageItemNotFound());
        $this->assertEquals('Порядок записей изменён', TestingDictionary::messageItemsReordered());
    }

    public function test_dictionary_get_titles(): void
    {
        App::setLocale('en');
        $this->assertEquals([
            'name' => 'Name',
            'value' => 'Name',
        ], TestingDictionary::fieldTitles());

        App::setLocale('ru');
        $this->assertEquals([
            'name' => 'Название',
            'value' => 'Название',
        ], TestingDictionary::fieldTitles());
    }

    public function test_dictionary_get_types(): void
    {
        $this->assertEquals([
            'name' => 'string',
            'value' => 'string',
        ], TestingDictionary::fieldTypes());

        $this->assertEquals([
            'name' => 'string',
        ], TestingDictionary::fieldTypes(true));
    }

    public function test_dictionary_get_rules(): void
    {
        $this->assertEquals([
            'name' => 'required|unique',
            'value' => 'required',
        ], TestingDictionary::fieldRules());
    }

    public function test_dictionary_get_validation_messages(): void
    {
        App::setLocale('en');
        $this->assertEquals([
            'name.required' => 'The :attribute field is required.',
            'name.unique' => 'The :attribute has already been taken.',
            'value.required' => 'The :attribute field is required.',
        ], TestingDictionary::fieldMessages());

        App::setLocale('ru');
        $this->assertEquals([
            'name.required' => 'Поле :attribute обязательно для заполнения.',
            'name.unique' => 'Такое значение поля :attribute уже существует.',
            'value.required' => 'Поле :attribute обязательно для заполнения.',
        ], TestingDictionary::fieldMessages());
    }

    public function test_dictionary_permissions_no_access(): void
    {
        TestingDictionary::$viewPermissions = false;
        TestingDictionary::$editPermissions = false;

        $current = $this->initCurrent(PositionType::admin);
        $this->assertFalse(TestingDictionary::canView($current));
        $this->assertFalse(TestingDictionary::canEdit($current));

        $current = $this->initCurrent(PositionType::staff);
        $this->assertFalse(TestingDictionary::canView($current));
        $this->assertFalse(TestingDictionary::canEdit($current));
    }

    public function test_dictionary_permissions_full_access(): void
    {
        TestingDictionary::$viewPermissions = true;
        TestingDictionary::$editPermissions = true;

        $current = $this->initCurrent(PositionType::admin);
        $this->assertTrue(TestingDictionary::canView($current));
        $this->assertTrue(TestingDictionary::canEdit($current));

        $current = $this->initCurrent(PositionType::staff);
        $this->assertTrue(TestingDictionary::canView($current));
        $this->assertTrue(TestingDictionary::canEdit($current));
    }

    public function test_dictionary_permissions_permission_access(): void
    {
        $currentAdmin = $this->initCurrent(PositionType::admin);
        $currentStaff = $this->initCurrent(PositionType::staff, ['testing.dictionary']);

        TestingDictionary::$viewPermissions = [PositionType::staff => ['testing.dictionary']];
        TestingDictionary::$editPermissions = false;
        $this->assertFalse(TestingDictionary::canView($currentAdmin));
        $this->assertFalse(TestingDictionary::canEdit($currentAdmin));
        $this->assertTrue(TestingDictionary::canView($currentStaff));
        $this->assertFalse(TestingDictionary::canEdit($currentStaff));

        TestingDictionary::$viewPermissions = false;
        TestingDictionary::$editPermissions = [PositionType::staff => ['testing.dictionary']];
        $this->assertFalse(TestingDictionary::canView($currentAdmin));
        $this->assertFalse(TestingDictionary::canEdit($currentAdmin));
        $this->assertFalse(TestingDictionary::canView($currentStaff));
        $this->assertTrue(TestingDictionary::canEdit($currentStaff));

        TestingDictionary::$viewPermissions = [PositionType::admin => true, PositionType::staff => ['testing.dictionary']];
        TestingDictionary::$editPermissions = [PositionType::admin => true, PositionType::staff => ['testing.dictionary']];
        $this->assertTrue(TestingDictionary::canView($currentAdmin));
        $this->assertTrue(TestingDictionary::canEdit($currentAdmin));
        $this->assertTrue(TestingDictionary::canView($currentStaff));
        $this->assertTrue(TestingDictionary::canEdit($currentStaff));
    }
}
