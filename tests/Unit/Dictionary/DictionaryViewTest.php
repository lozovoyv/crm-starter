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

use App\Dictionaries\Base\DictionaryViewDTO;
use Carbon\Carbon;
use Tests\TestCase;

class DictionaryViewTest extends TestCase
{
    public function test_dictionary_view(): void
    {
        $now = Carbon::now();
        $view = new DictionaryViewDTO(['test'], $now, false, false);
        $this->assertEquals(['test'], $view->items());
        $this->assertEquals($now, $view->lastModified());
        $this->assertFalse($view->isNotModified());
        $this->assertFalse($view->isEditable());
    }
}
