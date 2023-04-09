<?php
declare(strict_types=1);

namespace App\Interfaces;

interface HashCheckable
{
    /**
     * Make hash to check modifications.
     *
     * @return  string|null
     */
    public function getHash(): ?string;

    /**
     * Check hash against given.
     *
     * @param string|null $hash
     *
     * @return  bool
     */
    public function isHash(?string $hash): bool;

    /**
     * Make hash for model.
     *
     * @return  string|null
     */
    public function hash(): ?string;
}
