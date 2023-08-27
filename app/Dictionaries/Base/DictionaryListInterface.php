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

interface DictionaryListInterface
{
    /**
     * Dictionary items.
     *
     * @return Collection|array
     */
    public function items(): Collection|array;

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
    public function types(): array;
}
