<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\History;

use App\Http\Controllers\ApiHistoryController;
use App\Models\History\History;
use Illuminate\Database\Eloquent\Builder;

class HistoryController extends ApiHistoryController
{
    protected function getQuery(array $with = []):Builder
    {
        return History::query()
            ->with($with);
    }
}
