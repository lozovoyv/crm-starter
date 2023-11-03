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

namespace Tests\Unit\Traits;

use App\Models\History\HistoryChange;
use App\Models\Traits\SetAttributeWithChanges;
use App\Utils\Casting;
use Tests\TestCase;

class SetAttributeWithChangesTest extends TestCase
{
    use SetAttributeWithChanges;

    protected mixed $value = null;
    protected bool $exists = false;

    public function test_set_attribute_with_changes_null(): void
    {
        $changes = $this->setAttributeWithChanges('value', null);

        $this->assertNull($changes);
        $this->assertNull($this->value);
    }

    public function test_set_attribute_with_changes_string_no_type(): void
    {
        $changes = $this->setAttributeWithChanges('value', 'test');

        $this->assertNull($changes);
        $this->assertEquals('test', $this->value);
    }

    public function test_set_attribute_with_changes_string_type(): void
    {
        /** @var HistoryChange $changes */
        $changes = $this->setAttributeWithChanges('value', 'test', Casting::string);

        $this->assertEquals('value', $changes->parameter);
        $this->assertEquals(Casting::string, $changes->type);
        $this->assertNull($changes->old);
        $this->assertEquals('test', $changes->new);
        $this->assertEquals('test', $this->value);
    }
}
