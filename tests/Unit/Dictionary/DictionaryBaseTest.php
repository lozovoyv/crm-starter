<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class DictionaryBaseTest extends TestCase
{
    public function test_dictionary_base_titles_and_messages(): void
    {
        App::setLocale('en');
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

    public function test_dictionary_base_get_titles(): void
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

    public function test_dictionary_base_get_types(): void
    {
        $this->assertEquals([
            'name' => 'string',
            'value' => 'string',
        ], TestingDictionary::fieldTypes());

        $this->assertEquals([
            'name' => 'string',
        ], TestingDictionary::fieldTypes(true));
    }

    public function test_dictionary_base_get_rules(): void
    {
        $this->assertEquals([
            'name' => 'required|unique',
            'value' => 'required',
        ], TestingDictionary::fieldRules());
    }

    public function test_dictionary_base_get_validation_messages(): void
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
}
