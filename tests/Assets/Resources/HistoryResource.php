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

namespace Tests\Assets\Resources;

use App\Models\EntryScope;
use App\Models\History\History;
use App\Resources\HistoryResource as BaseHistoryResource;

class HistoryResource extends BaseHistoryResource
{
    public function __construct()
    {
        $this->query = History::query()
            ->withCount(['comments', 'links', 'changes'])
            ->where('entry_name', EntryScope::user);
    }
}