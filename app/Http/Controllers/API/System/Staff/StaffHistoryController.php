<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\API\HistoryBaseController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\Permissions\Permission;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Utils\Translate;

class StaffHistoryController extends HistoryBaseController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__staff),
        ]);
    }

    /**
     * All staff history list.
     *
     * @param APIListRequest $request
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * All staff history entry comments.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function comments(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All staff history entry changes.
     *
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function change(int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }

    /**
     * Certain staff history list.
     *
     * @param APIListRequest $request
     * @param int $positionID
     *
     * @return ApiResponse
     */
    public function listForStaff(APIListRequest $request, int $positionID): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->whereEntryID($positionID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * Certain staff history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function commentsForStaff(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->whereEntryID($positionID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain staff history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function changeForStaff(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::staff)])
            ->whereEntryID($positionID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }

    /**
     * Certain staff operations history list.
     *
     * @param APIListRequest $request
     * @param int $positionID
     *
     * @return ApiResponse
     */
    public function listByStaff(APIListRequest $request, int $positionID): ApiResponse
    {
        $list = History::query()
            ->whereOperator($positionID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * Certain staff operations history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function commentsByStaff(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereOperator($positionID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain staff operations history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function changeByStaff(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereOperator($positionID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }
}
