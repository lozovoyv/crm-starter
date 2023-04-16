<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiHistoryController;
use App\Models\EntryScope;
use App\Models\History\History;
use Illuminate\Database\Eloquent\Builder;

class PermissionsHistoryController extends ApiHistoryController
{
    protected function getQuery(array $with = []):Builder
    {
        return History::query()
            ->with($with)
            ->where('entry_name', EntryScope::permission_group);
    }
}
