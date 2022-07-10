<?php

namespace App\Traits;

use BadMethodCallException;

/**
 * @method string hash()
 */
trait HashCheck
{
    /**
     * Make hash to check modifications.
     *
     * @return  string
     */
    public function getHash(): string
    {
        if (!method_exists($this, 'hash')) {
            throw new BadMethodCallException('Undefined `hash()` method on ' . __CLASS__);
        }

        return md5($this->hash());
    }

    /**
     * Check hash against given.
     *
     * @param string $hash
     *
     * @return bool
     */
    public function isHash(string $hash): bool
    {
        return $this->getHash() === $hash;
    }
}
