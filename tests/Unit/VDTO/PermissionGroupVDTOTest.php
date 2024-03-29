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

namespace Tests\Unit\VDTO;

use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionScope;
use App\VDTO\PermissionGroupVDTO;
use Tests\TestCase;
use Tests\Traits\AssertLocalizations;

class PermissionGroupVDTOTest extends TestCase
{
    use AssertLocalizations;

    public function test_vdto_attributes(): void
    {
        $vdto = new PermissionGroupVDTO([
            'name' => 'PermissionGroupVDTOTest',
            'active' => true,
            'description' => 'Description',
        ]);

        $this->assertEquals('PermissionGroupVDTOTest', $vdto->name);
        $this->assertTrue($vdto->active);
        $this->assertEquals('Description', $vdto->description);
        $this->assertEmpty($vdto->permissions);
    }

    public function test_vdto_titles(): void
    {
        $vdto = new PermissionGroupVDTO();
        $this->assertLocalizations([
            'name' => 'permissions/permission_group.field_name',
            'active' => 'permissions/permission_group.field_active',
            'description' => 'permissions/permission_group.field_description',
            'permissions' => 'permissions/permission_group.field_permissions',
        ], [$vdto, 'getTitles']);
        $this->assertLocalizations(['name' => 'permissions/permission_group.field_name'], [$vdto, 'getTitles'], ['name']);
        $this->assertEquals([], $vdto->getTitles(['not_exists']));
    }

    public function test_vdto_validation_messages(): void
    {
        $vdto = new PermissionGroupVDTO();
        $this->assertLocalizations(['name.unique' => 'permissions/permission_group.validation_name_unique',], [$vdto, 'getValidationMessages']);
        $this->assertLocalizations(['name.unique' => 'permissions/permission_group.validation_name_unique',], [$vdto, 'getValidationMessages'], ['name']);
        $this->assertEquals([], $vdto->getValidationMessages(['not_exists']));
    }

    public function test_vdto_validation_rules(): void
    {
        $vdto = new PermissionGroupVDTO();
        $this->assertEquals([
            'name' => 'required',
            'active' => 'required|boolean',
            'description' => 'nullable',
            'permissions' => 'nullable',
            'permissions.*' => 'required|exists:permissions,id',
        ], $vdto->getValidationRules());
        $this->assertEquals(['name' => 'required'], $vdto->getValidationRules(['name']));
        $this->assertEquals([], $vdto->getValidationRules(['not_exists']));
    }

    public function test_vdto_validate_fails(): void
    {
        $vdto = new PermissionGroupVDTO([
            'name' => null,
            'active' => 123,
            'description' => null,
            'permissions' => [
                '0',
            ],
        ]);

        $this->assertArrayHasKey('name', $vdto->validate(['name']));

        $errors = $vdto->validate();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('active', $errors);
        $this->assertArrayNotHasKey('description', $errors);
        $this->assertArrayHasKey('permissions.0', $errors);
    }

    public function test_vdto_validate_success(): void
    {
        // Create test permission
        $permission = Permission::query()->where('key', 'vdto__test')->first();
        if ($permission === null) {
            $scope = new PermissionScope(['scope_name' => 'vdto', 'name' => 'test']);
            $scope->save();
            $permission = new Permission(['key' => 'vdto__test', 'scope_name' => 'vdto', 'name' => 'Test permission']);
            $permission->save();
        }

        $vdto = new PermissionGroupVDTO([
            'name' => 'PermissionGroupVDTOTest',
            'active' => true,
            'description' => 'Description',
            'permissions' => [
                $permission->id,
            ],
        ]);

        $this->assertNull($vdto->validate());
    }
}
