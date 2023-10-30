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

namespace App\Resources;

abstract class EntryResource
{
    protected ?string $formTitle = null;

    /**
     * Get assigned edit form title.
     *
     * @return string|null
     */
    public function formTitle(): ?string
    {
        return !empty($this->formTitle) ? trans($this->formTitle) : null;
    }
}
