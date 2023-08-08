<?php
declare(strict_types=1);

namespace App\Traits;

use BadMethodCallException;
use Carbon\Carbon;

/**
 * @property Carbon|null $updated_at
 */
trait HashCheck
{
    /**
     * Instance hash.
     *
     * @return  string|null
     */
    public function hash(): ?string
    {
        if (empty($this->updated_at)) {
            return null;
        }

        return $this->updated_at instanceof Carbon ? $this->updated_at->toString() : (string)$this->updated_at;
    }

    /**
     * Make hash to check modifications.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
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
