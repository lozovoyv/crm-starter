<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\API\HistoryBaseController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\PositionType;
use App\Utils\Translate;

class PermissionGroupHistoryController extends HistoryBaseController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__permissions),
        ]);
    }

    /**
     * Permissions groups history list.
     *
     * @param APIListRequest $request
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(PermissionGroup::class)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * Permissions group history comments.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function comments(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(PermissionGroup::class)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return $this->notFoundResponse();
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Permissions group history change.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function change(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(PermissionGroup::class)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return $this->notFoundResponse();
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }
}
