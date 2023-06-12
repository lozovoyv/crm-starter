<?php
declare(strict_types=1);

namespace App\Resources\Users;

use App\Models\EntryScope;
use App\Models\History\History;
use App\Resources\HistoryResource;

class UserHistoryResource extends HistoryResource
{
    public function __construct()
    {
        $this->query = History::query()
            ->withCount(['comments', 'links', 'changes'])
            ->where('entry_name', EntryScope::user);
    }
}
