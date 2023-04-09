<?php
declare(strict_types=1);

namespace App\Dictionaries;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface DictionaryViewInterface
{
    /**
     * Dictionary items.
     *
     * @return Collection|array|null
     */
    public function items(): Collection|array|null;

    /**
     * Last modification date.
     *
     * @return Carbon|null
     */
    public function lastModified(): ?Carbon;

    /**
     * Is last modification date.
     *
     * @return bool
     */
    public function isNotModified(): bool;
}
