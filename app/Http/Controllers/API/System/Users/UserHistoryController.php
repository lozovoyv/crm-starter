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

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\API\HistoryBaseController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Utils\Translate;

class UserHistoryController extends HistoryBaseController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__users),
        ]);
    }

    /**
     * All users history list.
     *
     * @param APIListRequest $request
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(User::class)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * All users history entry comments.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function comments(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(User::class)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All users history entry changes.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function change(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(User::class)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }

    /**
     * Certain user history list.
     *
     * @param APIListRequest $request
     * @param int $userID
     *
     * @return ApiResponse
     */
    public function listForUser(APIListRequest $request, int $userID): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(User::class)
            ->whereEntryID($userID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * Certain user history entry comments.
     *
     * @param int $userID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function commentsForUser(int $userID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(User::class)
            ->whereEntryID($userID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain user history entry changes.
     *
     * @param int $userID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function changeForUser(int $userID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(User::class)
            ->whereEntryID($userID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }
}
