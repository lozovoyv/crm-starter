<?php

namespace App\Http\Controllers\API\System\Staff;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffViewController extends ApiController
{
    public function view(Request $request): JsonResponse
    {
        /** @var Position|null $staff */
        $staff = Position::query()
            ->with(['status', 'type', 'user'])
            ->where('type_id', PositionType::staff)
            ->where('id', $request->input('id'))
            ->first();

        if ($staff === null) {
            return APIResponse::notFound('Сотрудник не найден');
        }

        return APIResponse::response([
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
