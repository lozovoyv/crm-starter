<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use Illuminate\Http\Request;

class StaffViewController extends ApiController
{
    public function view(Request $request): ApiResponse
    {
        /** @var Position|null $staff */
        $staff = Position::query()
            ->with(['status', 'type', 'user'])
            ->where('type_id', PositionType::staff)
            ->where('id', $request->input('id'))
            ->first();

        if ($staff === null) {
            return ApiResponse::notFound('Сотрудник не найден');
        }

        return APIResponse::common([
            'id' => $staff->id,
            'active' => $staff->hasStatus(PositionStatus::active),
            'status' => $staff->status->name,
            'type' => $staff->type->name,
            'status_id' => $staff->id,
            'name' => $staff->user->fullName,
            'user' => $staff->user,
        ]);
    }
}
