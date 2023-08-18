<?php
declare(strict_types=1);

namespace App\Dictionaries\Base;

use Illuminate\Support\Collection;

interface DictionaryListInterface
{
    /**
     * Dictionary items.
     *
     * @return Collection|array|null
     */
    public function items(): Collection|array|null;

    /**
     * Dictionary title.
     *
     * @return string
     */
    public function title(): string;

    /**
     * Dictionary item props titles.
     *
     * @return array
     */
    public function titles(): array;

    /**
     * Whether is dictionary sortable.
     *
     * @return bool
     */
    public function orderable(): bool;

    /**
     * Whether is dictionary items switchable.
     *
     * @return bool
     */
    public function switchable(): bool;

    /**
     * Dictionary items fields details.
     *
     * @return array
     */
    public function fields(): array;
}
