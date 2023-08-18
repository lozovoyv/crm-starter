<?php
declare(strict_types=1);

namespace App\Dictionaries\Base;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface DictionaryViewContainerInterface
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

    /**
     * Is dictionary editable.
     *
     * @return bool
     */
    public function isEditable(): bool;
}
