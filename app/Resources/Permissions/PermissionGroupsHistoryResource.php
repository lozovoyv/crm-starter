<?php
declare(strict_types=1);

namespace App\Resources\Permissions;

use App\Models\EntryScope;
use App\Models\History\History;
use App\Resources\HistoryResource;

class PermissionGroupsHistoryResource extends HistoryResource
{
    public function __construct()
    {
        $this->query = History::query()
            ->withCount(['comments', 'links', 'changes'])
            ->where('entry_name', EntryScope::permission_group);
    }
}
