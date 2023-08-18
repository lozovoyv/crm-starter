<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use App\Dictionaries\Base\Dictionary;

class TestingDictionary extends Dictionary
{
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
