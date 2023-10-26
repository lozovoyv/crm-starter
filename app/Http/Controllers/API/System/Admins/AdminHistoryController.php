<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Admins;

use App\Http\Controllers\API\HistoryBaseController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\Positions\Position;
use App\Models\Positions\PositionType;
use App\Utils\Translate;

class AdminHistoryController extends HistoryBaseController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin),
        ]);
    }

    /**
     * All admin history list.
     *
     * @param APIListRequest $request
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * All admin history entry comments.
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
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All admin history entry changes.
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
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list($history->getChanges())
            ->titles(Translate::array($this->changesTitles));
    }

    /**
     * Certain admin history list.
     *
     * @param APIListRequest $request
     * @param int $positionID
     *
     * @return ApiResponse
     */
    public function listForAdmin(APIListRequest $request, int $positionID): ApiResponse
    {
        $list = History::query()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
            ->whereEntryID($positionID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection('desc'))
            ->pagination($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }

    /**
     * Certain admin history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function commentsForAdmin(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withComments()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
            ->whereEntryID($positionID)
            ->whereID($historyID)
            ->first();

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain admin history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function changeForAdmin(int $positionID, int $historyID): ApiResponse
    {
        $history = History::query()
            ->withChanges()
            ->whereEntryType(Position::class)
            ->whereEntryTags([PositionType::typeToString(PositionType::admin)])
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
     * Certain admin operations history list.
     *
     * @param APIListRequest $request
     * @param int $positionID
     *
     * @return ApiResponse
     */
    public function listByAdmin(APIListRequest $request, int $positionID): ApiResponse
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
     * Certain admin operations history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function commentsByAdmin(int $positionID, int $historyID): ApiResponse
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
     * Certain admin operations history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     *
     * @return ApiResponse
     */
    public function changeByAdmin(int $positionID, int $historyID): ApiResponse
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
