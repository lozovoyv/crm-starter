<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\VDTO;

use App\Models\Permissions\PermissionGroup;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property bool $active
 * @property string|null $description
 * @property array|null $permissions
 */
class PermissionGroupVDTO extends VDTO
{
    protected array $rules = [
        'name' => 'required',
        'active' => 'required|boolean',
        'description' => 'nullable',
        'permissions' => 'nullable',
        'permissions.*' => 'required|exists:permissions,id',
    ];

    protected array $titles = [
        'name' => 'permissions/permission_group.field_name',
        'active' => 'permissions/permission_group.field_active',
        'description' => 'permissions/permission_group.field_description',
        'permissions' => 'permissions/permission_group.field_permissions',
    ];

    protected array $messages = [
      'name.unique' => 'permissions/permission_group.validation_name_unique',
    ];

    /**
     * Validate data and return validation errors.
     *
     * @param array $only
     * @param PermissionGroup|null $group
     *
     * @return  array|null
     */
    public function validate(array $only = [], PermissionGroup $group = null): ?array
    {
        $rules = $this->rules;
        $rules['name'] = [...explode('|', $rules['name']), Rule::unique('permission_groups', 'name')->ignore($group)];

        if (!empty($only)) {
            $rules = Arr::only($rules, $only);
        }

        return $this->validateAttributes($this->attributes, $rules, $this->titles, $this->messages);
    }
}
