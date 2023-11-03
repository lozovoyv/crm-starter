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

use App\Models\Interfaces\HashCheckable;
use App\Models\Traits\HashCheck;
use Tests\TestCase;

class HashCheckTest extends TestCase implements HashCheckable
{
    use HashCheck;

    protected ?string $hash;

    protected $updated_at;

    public function test_hash_check():void
    {
        $this->updated_at = 'test';
        $hash = $this->hash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }

    public function test_hash_check_null():void
    {
        $this->updated_at = null;
        $hash = $this->hash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }
}
