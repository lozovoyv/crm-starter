<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class RolesController extends ApiController
{
    /**
     * Deactivate role.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function deactivate(Request $request): ApiResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $role->active = false;
        $role->save();

        $current = Current::init($request);
        $role
            ->addHistory(HistoryAction::permission_role_deactivated, $current->positionId());

        return APIResponse::common($role)->message('Роль отключена');
    }

    /**
     * Activate role.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function activate(Request $request): ApiResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $role->active = true;
        $role->save();

        $current = Current::init($request);
        $role
            ->addHistory(HistoryAction::permission_role_activated, $current->positionId());

        return APIResponse::common($role)->message('Роль включена');
    }

    /**
     * Delete role.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function remove(Request $request): ApiResponse
    {
        try {
            $role = $this->getRole($request);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $changes = [
            ['parameter' => 'name', 'type' => Casting::string, 'old' => $role->name, 'new' => null],
            ['parameter' => 'active', 'type' => Casting::bool, 'old' => $role->active, 'new' => null],
            ['parameter' => 'description', 'type' => Casting::string, 'old' => $role->description, 'new' => null],
            ['parameter' => 'permissions', 'type' => Casting::array, 'old' => $role->permissions()->pluck('id')->toArray(), 'new' => null],
        ];

        $current = Current::init($request);

        try {
            DB::transaction(static function () use ($role, $changes, $current) {
                $role
                    ->addHistory(HistoryAction::permission_role_deleted, $current->positionId())
                    ->addChanges($changes);

                $role->delete();
            });
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить роль.');
        }

        return APIResponse::success('Роль удалена');
    }

    /**
     * Get role.
     *
     * @param Request $request
     *
     * @return PermissionGroup
     * @throws Exception
     */
    protected function getRole(Request $request): PermissionGroup
    {
        /** @var PermissionGroup|null $role */
        $role = PermissionGroup::query()->where('id', $request->input('role_id'))->first();

        if ($role === null) {
            throw new InvalidArgumentException('Роль не найдена');
        }
        if (!$role->isHash($request->input('role_hash'))) {
            throw new RuntimeException('Роль была изменена в другом месте.');
        }
        if ($role->locked) {
            throw new RuntimeException('Эту роль нельзя изменить или удалить');
        }

        return $role;
    }
}
