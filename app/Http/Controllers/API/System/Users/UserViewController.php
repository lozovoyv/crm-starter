<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Resources\Users\UserEntryResource;
use Exception;

class UserViewController extends ApiController
{
    /**
     * User view.
     *
     * @param int $userID
     * @param UserEntryResource $resource
     *
     * @return ApiResponse
     */
    public function view(int $userID, UserEntryResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound($exception->getMessage());
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $data = UserEntryResource::format($user);
        $data['name'] = $user->compactName;

        return APIResponse::common($data);
    }
}
