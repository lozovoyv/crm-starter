<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Http\APIResponse;
use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use App\Models\History\HistoryScope;
use Illuminate\Http\JsonResponse;

class RolesHistoryController extends ApiHistoryController
{
    /**
     * Get roles list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = History::query()->where('entry_name', HistoryScope::role);

        $history = $this->retrieveHistory($query, $request);

        return APIResponse::list(
            $history,
            $this->titles,
            $this->filters,
            $this->defaultFilters,
            $request->search(true),
            $this->order,
            $this->orderBy,
            $this->ordering
        );
    }
}
