<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Current;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChanges;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;
use UnexpectedValueException;

class PermissionGroupCRUDController extends ApiController
{
    protected array $rules = [
        'name' => 'required',
        'active' => 'required',
        'description' => 'nullable',
    ];

    protected array $titles = [
        'name' => 'Название',
        'active' => 'Статус',
        'description' => 'Описание',
    ];

    /**
     * Get permission group data for edit.
     *
     * @param int|null $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function get(Request $request, ?int $id = null): ApiResponse
    {
        try {
            $group = $this->getPermissionGroup($id, null, false, Casting::fromString($request->input('from_group_id'), Casting::int));
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $values = [
            'name' => $group->name,
            'active' => $group->active,
            'description' => $group->description,
        ];
        $ids = [];
        $descriptions = [];
        $scopes = [];

        $permissions = Permission::query()
            ->with(['scope', 'groups' => function (BelongsToMany $query) use ($group) {
                $query->where('id', $group->id);
            }])
            ->orderBy('order')
            ->get();

        $permissions->map(function (Permission $permission) use (&$values, &$ids, &$descriptions, &$scopes) {
            $this->titles['permission.' . $permission->id] = $permission->name;
            $values['permission.' . $permission->id] = $permission->groups->count() !== 0;
            $ids[] = $permission->id;
            $descriptions[$permission->id] = $permission->description;
            $scopes[$permission->id] = $permission->scope->name;
        });

        return APIResponse::form()
            ->title($group->exists ? $group->name : 'Добавление группы прав')
            ->values($values)
            ->hash($group->getHash())
            ->rules($this->rules)
            ->titles($this->titles)
            ->payload([
                'scopes' => $scopes,
                'permissions' => $ids,
                'descriptions' => $descriptions,
            ]);
    }

    /**
     * Update or create permission group.
     *
     * @param int|null $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function update(Request $request, ?int $id = null): ApiResponse
    {
        try {
            $group = $this->getPermissionGroup($id, $request->input('hash'), true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $data = $this->data($request);

        $this->rules['name'] = ['required', Rule::unique('permission_groups', 'name')->ignore($group->id)];

        if ($errors = $this->validate($data, $this->rules, $this->titles)) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $hasChanges = false;

        DB::transaction(function () use ($group, $data, $current, &$hasChanges) {
            $changes = [];
            $changes[] = $this->set($group, 'name', $data['name'], Casting::string);
            $changes[] = $this->set($group, 'active', $data['active'], Casting::bool);
            $changes[] = $this->set($group, 'description', $data['description'], Casting::string);
            $group->save();

            $ids = [];

            $existingIds = Permission::query()->pluck('id')->toArray();
            $oldIds = $group->permissions()->pluck('id')->toArray();

            foreach ($existingIds as $existingId) {
                if (!empty($data['permission.' . $existingId])) {
                    $ids[] = $existingId;
                }
            }

            sort($ids);
            sort($oldIds);

            $changed = $group->permissions()->sync($ids);

            if (count($changed['attached']) || count($changed['updated']) || count($changed['detached'])) {
                $group->touch();
                $changes[] = new HistoryChanges(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $oldIds, 'new' => $ids]);
            }

            $changes = array_filter($changes);

            if (!empty($changes)) {
                $group
                    ->addHistory($group->wasRecentlyCreated ? HistoryAction::permission_group_created : HistoryAction::permission_group_edited, $current->positionId())
                    ->addChanges($changes);
                $hasChanges = true;
            }
        });

        if (!$hasChanges) {
            return APIResponse::success('Изменений не сделано');
        }

        return APIResponse::success($group->wasRecentlyCreated ? 'Группа прав добавлена' : 'Группа прав сохранена');
    }

    /**
     * Delete permission group.
     *
     * @param int $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function remove(Request $request, int $id): ApiResponse
    {
        try {
            $group = $this->getPermissionGroup($id, $request->input('hash'), true, null, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $changes = [
            new HistoryChanges(['parameter' => 'name', 'type' => Casting::string, 'old' => $group->name, 'new' => null]),
            new HistoryChanges(['parameter' => 'active', 'type' => Casting::bool, 'old' => $group->active, 'new' => null]),
            new HistoryChanges(['parameter' => 'description', 'type' => Casting::string, 'old' => $group->description, 'new' => null]),
            new HistoryChanges(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $group->permissions()->pluck('id')->toArray(), 'new' => null]),
        ];

        $current = Current::init($request);

        try {
            DB::transaction(static function () use ($group, $changes, $current) {
                $group
                    ->addHistory(HistoryAction::permission_group_deleted, $current->positionId())
                    ->addChanges($changes);

                $group->delete();
            });
        } catch (QueryException) {
            return APIResponse::error('Невозможно удалить группу прав');
        }

        return APIResponse::success('Группа прав удалена');
    }

    /**
     * Change permission group status.
     *
     * @param int $id
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function change(int $id, Request $request): ApiResponse
    {
        try {
            $group = $this->getPermissionGroup($id, $request->input('hash'), true, null, true);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $group->active = !$request->boolean('disable', false);
        $group->save();

        $current = Current::init($request);

        $action = $group->active ? HistoryAction::permission_group_activated : HistoryAction::permission_group_deactivated;

        $group->addHistory($action, $current->positionId());

        return APIResponse::common($group)->message($group->active ? 'Группа прав включена' : 'Группа прав отключена');
    }

    /**
     * Get permission group.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param int|null $overrideId
     * @param bool $onlyExisting
     *
     * @return PermissionGroup
     */
    protected function getPermissionGroup(?int $id, ?string $hash, bool $check, ?int $overrideId = null, bool $onlyExisting = false): PermissionGroup
    {
        /** @var PermissionGroup|null $group */
        if ($id === null) {
            if ($overrideId !== null) {
                $group = PermissionGroup::query()->where('id', $overrideId)->first();
                if ($group) {
                    $group->exists = false;
                }
            } else {
                $group = $onlyExisting ? null : new PermissionGroup();
            }
        } else {
            $group = PermissionGroup::query()->where('id', $id)->first();
        }

        if ($group === null) {
            throw new UnexpectedValueException('Группа прав не найдена');
        }
        if ($check && $group->exists && !$group->isHash($hash)) {
            throw new RuntimeException('Группа прав была изменена в другом месте.');
        }
        if ($group->locked) {
            throw new RuntimeException('Эту группу прав нельзя изменить или удалить');
        }

        return $group;
    }
}
