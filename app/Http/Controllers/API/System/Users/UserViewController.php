<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Users\User;
use Illuminate\Http\Request;

class UserViewController extends ApiController
{
    public function view(Request $request): ApiResponse
    {
        /** @var User|null $user */
        $user = User::query()
            ->with(['status'])
            ->where('id', $request->input('id'))
            ->first();

        if ($user === null) {
            return ApiResponse::notFound('Учётная запись не найдена');
        }

        $data = $user->toArray();

        $data['name'] = $user->compactName;

        return APIResponse::common($data);
    }
}
