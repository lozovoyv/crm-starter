<?php
declare(strict_types=1);

namespace App\Traits;

use BadMethodCallException;

/**
 * @method string|null hash()
 */
trait HashCheck
{
    /**
     * Make hash to check modifications.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        if (!method_exists($this, 'hash')) {
            throw new BadMethodCallException('Undefined `hash()` method on ' . __CLASS__);
        }

        $hash = $this->hash();

        return $hash ? md5($this->hash()) : null;
    }

    /**
     * Check hash against given.
     *
     * @param string|null $hash
     *
     * @return bool
     */
    public function isHash(?string $hash): bool
    {
        return $this->getHash() === $hash;
    }
}
