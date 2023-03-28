<?php
declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Interfaces\HashCheckable;
use App\Traits\HashCheck;
use PHPUnit\Framework\TestCase;

class HashCheckTest extends TestCase implements HashCheckable
{
    use HashCheck;

    protected ?string $hash;

    /**
     * Make hash for model.
     *
     * @return  string|null
     */
    public function hash(): ?string
    {
        return $this->hash;
    }

    public function test_hash_check():void
    {
        $this->hash = 'test';
        $hash = $this->getHash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }

    public function test_hash_check_null():void
    {
        $this->hash = null;
        $hash = $this->getHash();

        $this->assertTrue($this->isHash($hash));
        $this->assertFalse($this->isHash(''));
    }
}
