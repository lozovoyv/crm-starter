<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

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
