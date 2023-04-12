<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiHistoryController;
use App\Models\History\History;
use App\Models\EntryScope;
use Illuminate\Database\Eloquent\Builder;

class UserHistoryController extends ApiHistoryController
{
    protected function getQuery(array $with, int $userID): Builder
    {
        return History::query()
            ->with($with)
            ->where('entry_name', EntryScope::user)
            ->where('entry_id', $userID);
    }
}
