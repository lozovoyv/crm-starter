<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiHistoryController;
use App\Models\History\History;
use App\Models\EntryScope;
use Illuminate\Database\Eloquent\Builder;

class UserHistoryController extends ApiHistoryController
{
    protected function getHistoryID(array $args): ?int
    {
        return (int)array_pop($args);
    }

    protected function getArguments(array $args): array
    {
        array_pop($args);

        return $args;
    }

    protected function getQuery(array $with, $userID): Builder
    {
        return History::query()
            ->with($with)
            ->where('entry_name', EntryScope::user)
            ->where('entry_id', (int)$userID);
    }
}
