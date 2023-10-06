<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Users\UserResource;
use Exception;

class UserViewController extends ApiController
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
     * User view.
     *
     * @param int $userID
     * @param UserResource $resource
     *
     * @return ApiResponse
     */
    public function __invoke(int $userID, UserResource $resource): ApiResponse
    {
        try {
            $user = $resource->get($userID);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound($exception->getMessage());
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $data = UserResource::format($user);
        $data['name'] = $user->compactName;

        return APIResponse::common($data);
    }
}
