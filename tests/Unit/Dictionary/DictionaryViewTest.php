<?php
declare(strict_types=1);

namespace Tests\Unit\Dictionary;

use App\Dictionaries\Base\DictionaryView;
use Carbon\Carbon;
use Tests\TestCase;

class DictionaryViewTest extends TestCase
{
    public function test_dictionary_view(): void
    {
        $now = Carbon::now();
        $view = new DictionaryView(['test'], $now, false, false);
        $this->assertEquals(['test'], $view->items());
        $this->assertEquals($now, $view->lastModified());
        $this->assertFalse($view->isNotModified());
        $this->assertFalse($view->isEditable());
    }
}
