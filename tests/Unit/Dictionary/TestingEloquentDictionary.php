<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use App\Dictionaries\Base\EloquentDictionary;

class TestingEloquentDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = FakeEloquentDictionary::class;

    protected static bool $orderable = true;

    protected static string $id_field = 'id';
    protected static string $name_field = 'name';
    protected static ?string $hint_field = 'hint';
    protected static ?string $enabled_field = 'enabled';
    protected static ?string $order_field = 'order';
    protected static ?string $locked_field = 'locked';
    protected static ?string $updated_at_field = 'updated_at';

    protected static array $fields = [
        'name' => [
            'title' => 'dictionaries/defaults.fields.name',
            'type' => 'string',
            'column' => 'name',
            'validation_rules' => 'required|unique',
            'validation_messages' => [
                'required' => 'validation.required',
                'unique' => 'validation.unique',
            ],
            'show' => true,
        ],
        'description' => [
            'title' => 'dictionaries/defaults.fields.name',
            'type' => 'string',
            'column' => 'value',
            'validation_rules' => 'required',
            'validation_messages' => [
                'required' => 'validation.required',
            ],
            'show' => true,
        ],
        'value' => [
            'title' => 'dictionaries/defaults.fields.name',
            'type' => 'string',
            'column' => 'value',
            'validation_rules' => 'required',
            'validation_messages' => [
                'required' => 'validation.required',
            ],
            'show' => false,
        ],
    ];
}
