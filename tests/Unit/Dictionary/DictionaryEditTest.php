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

namespace Tests\Unit\Dictionary;

use App\Dictionaries\Base\DictionaryEditDTODTO;
use Tests\TestCase;

class DictionaryEditTest extends TestCase
{
    public function test_dictionary_edit(): void
    {
        $edit = new DictionaryEditDTODTO(
            'id',
            'title',
            ['val' => 123],
            ['val' => 'required'],
            ['val' => 'Value'],
            [],
            'hash',
            ['val' => 'string'],
            ['val' => 0]
        );

        $this->assertEquals('id', $edit->id());
        $this->assertEquals('title', $edit->title());
        $this->assertEquals(['val' => 123], $edit->values());
        $this->assertEquals(['val' => 'required'], $edit->rules());
        $this->assertEquals(['val' => 'Value'], $edit->titles());
        $this->assertEquals([], $edit->messages());
        $this->assertEquals('hash', $edit->hash());
        $this->assertEquals(['val' => 'string'], $edit->types());
        $this->assertEquals(['val' => 0], $edit->options());
    }
}
