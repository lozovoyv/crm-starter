<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System;

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Users\UserEmailConfirmation;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EmailConfirmController extends ApiController
{
    public function confirm(Request $request): ApiResponse
    {
        $token = $request->input('token');

        /** @var UserEmailConfirmation|null $confirmation */
        $confirmation = UserEmailConfirmation::query()
            ->with('user')
            ->where('token', $token)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', Carbon::now());
            })
            ->first();

        if ($confirmation === null) {
            return ApiResponse::error('Не найден запрос на смену адреса электронной почты');
        }

        try {
            $confirmation->applyNewEmail();
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        return APIResponse::success('Адрес электронной почты ' . $confirmation->new_email . ' успешно подтверждён!');
    }
}
