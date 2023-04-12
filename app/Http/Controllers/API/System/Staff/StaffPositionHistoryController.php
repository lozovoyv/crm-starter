<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\ApiHistoryController;
use App\Models\EntryScope;
use App\Models\History\History;
use App\Models\Positions\PositionType;
use Illuminate\Database\Eloquent\Builder;

class StaffPositionHistoryController extends ApiHistoryController
{
    protected function getQuery(array $with, int $positionID): Builder
    {
        return History::query()
            ->with($with)
            ->where('entry_name', EntryScope::position)
            ->where('entry_id', $positionID)
            ->where('entry_type', PositionType::typeToString(PositionType::staff));
    }
}
