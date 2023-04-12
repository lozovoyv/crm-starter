<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\ApiHistoryController;
use App\Models\History\History;
use Illuminate\Database\Eloquent\Builder;

class StaffPositionOperationsController extends ApiHistoryController
{
    protected function getQuery(array $with, int $positionID): Builder
    {
        return History::query()
            ->with($with)
            ->where('position_id', $positionID);
    }
}
