<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Admins;

use App\Http\Controllers\ApiHistoryController;
use App\Models\EntryScope;
use App\Models\History\History;
use App\Models\Positions\PositionType;
use Illuminate\Database\Eloquent\Builder;

class AdminHistoryController extends ApiHistoryController
{
    protected function getQuery(array $with = []):Builder
    {
        return History::query()
            ->with($with)
            ->where('entry_name', EntryScope::position)
            ->where('entry_type', PositionType::typeToString(PositionType::admin));
    }
}
