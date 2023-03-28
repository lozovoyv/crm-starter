<?php
declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Traits\HashCheck;
use PHPUnit\Framework\TestCase;

class HashCheckTestFailMethod extends TestCase
{
    use HashCheck;

    public function test_hash_check_fail_method():void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->getHash();
    }
}
