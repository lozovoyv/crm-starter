<?php

namespace App\Http\Controllers\API\System\Users;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserViewController extends ApiController
{
    public function view(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = User::query()
            ->with(['status'])
            ->where('id', $request->input('id'))
            ->first();

        if ($user === null) {
            return APIResponse::notFound('Учётная запись не найдена');
        }

        $data = $user->toArray();

        $data['name'] = $user->compactName;

        return APIResponse::response($data);
    }
}
