<?php
declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Interfaces\HashCheckable;
use App\Traits\HashCheck;
use Tests\TestCase;

class HashCheckTest extends TestCase implements HashCheckable
{
    use HashCheck;

    protected ?string $hash;

    protected $updated_at;

    public function test_hash_check():void
    {
        $this->updated_at = 'test';
        $hash = $this->getHash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }

    public function test_hash_check_null():void
    {
        $this->updated_at = null;
        $hash = $this->getHash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }
}
