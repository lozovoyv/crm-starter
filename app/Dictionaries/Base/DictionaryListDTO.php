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

use Illuminate\Support\Collection;

class DictionaryListDTO implements DictionaryListDTOInterface
{
    /** @var Collection|array Dictionary items. */
    protected Collection|array $items;

    protected string $title;
    protected array $titles;
    protected bool $orderable;
    protected bool $switchable;
    protected array $types;

    /**
     * @param array|Collection $items
     * @param string $title
     * @param array $titles
     * @param bool $orderable
     * @param bool $switchable
     * @param array $types
     */
    public function __construct(array|Collection $items, string $title, array $titles, bool $orderable, bool $switchable, array $types)
    {
        $this->items = $items;
        $this->title = $title;
        $this->titles = $titles;
        $this->orderable = $orderable;
        $this->switchable = $switchable;
        $this->types = $types;
    }

    /**
     * Items getter.
     *
     * @return Collection|array
     */
    public function items(): Collection|array
    {
        return $this->items ?? [];
    }

    /**
     * Dictionary title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Dictionary item props titles.
     *
     * @return array
     */
    public function titles(): array
    {
        return $this->titles;
    }

    /**
     * Whether is dictionary sortable.
     *
     * @return bool
     */
    public function orderable(): bool
    {
        return $this->orderable;
    }

    /**
     * Whether is dictionary items switchable.
     *
     * @return bool
     */
    public function switchable(): bool
    {
        return $this->switchable;
    }

    /**
     * Dictionary items fields details.
     *
     * @return array
     */
    public function types(): array
    {
        return $this->types;
    }
}
