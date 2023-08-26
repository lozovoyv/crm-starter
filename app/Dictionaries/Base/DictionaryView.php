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

namespace App\Dictionaries\Base;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DictionaryView implements DictionaryViewInterface
{
    /** @var Collection|array|null Dictionary items. */
    protected Collection|array|null $items;

    /** @var Carbon|null Last modification date. */
    protected ?Carbon $lastModifiedAt;

    /** @var bool Whether dictionary modified since given timestamp. */
    protected bool $notModified = false;

    /** @var bool Whether dictionary could be edited. */
    protected bool $isEditable = false;

    /**
     * @param Collection|array|null $items
     * @param Carbon|null $lastModifiedAt
     * @param bool $notModified
     * @param bool $isEditable
     */
    public function __construct(Collection|array|null $items, Carbon|null $lastModifiedAt = null, bool $notModified = false, bool $isEditable = false)
    {
        $this->items = $items;
        $this->lastModifiedAt = $lastModifiedAt;
        $this->notModified = $notModified;
        $this->isEditable = $isEditable;
    }

    /**
     * Items getter.
     *
     * @return Collection|array|null
     */
    public function items(): Collection|array|null
    {
        return $this->items ?? null;
    }

    /**
     * Last modification time getter.
     *
     * @return Carbon|null
     */
    public function lastModified(): ?Carbon
    {
        return $this->lastModifiedAt ?? null;
    }

    /**
     * Not modified flag getter.
     *
     * @return bool
     */
    public function isNotModified(): bool
    {
        return $this->notModified;
    }

    /**
     * Is dictionary editable.
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->isEditable;
    }
}
