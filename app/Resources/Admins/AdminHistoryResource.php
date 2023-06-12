<?php
declare(strict_types=1);

namespace App\Resources\Admins;

use App\Models\EntryScope;
use App\Models\History\History;
use App\Models\Positions\PositionType;
use App\Resources\HistoryResource;

class AdminHistoryResource extends HistoryResource
{
    public function __construct()
    {
        $this->query = History::query()
            ->withCount(['comments', 'links', 'changes'])
            ->where('entry_name', EntryScope::position)
            ->where('entry_type', PositionType::typeToString(PositionType::admin));
    }
}
