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

namespace Tests\Assets\Dictionary;

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
