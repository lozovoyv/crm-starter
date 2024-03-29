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

use App\Dictionaries\Base\DictionaryListDTO;
use Tests\TestCase;

class DictionaryListTest extends TestCase
{
    public function test_dictionary_list(): void
    {
        $list = new DictionaryListDTO(
            ['item'],
            'title',
            ['val' => 'Value'],
            true,
            true,
            ['val' => 'string'],
        );

        $this->assertEquals(['item'], $list->items());
        $this->assertEquals('title', $list->title());
        $this->assertEquals(['val' => 'Value'], $list->titles());
        $this->assertTrue($list->orderable());
        $this->assertTrue( $list->switchable());
        $this->assertEquals(['val' => 'string'], $list->types());
    }
}
