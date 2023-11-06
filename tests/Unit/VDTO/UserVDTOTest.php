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

use App\Models\Users\UserStatus;
use App\VDTO\UserVDTO;
use Tests\TestCase;
use Tests\Traits\AssertLocalizations;

class UserVDTOTest extends TestCase
{
    use AssertLocalizations;

    public function test_vdto_attributes(): void
    {
        $vdto = new UserVDTO([
            'lastname' => 'Lastname',
            'firstname' => 'Firstname',
            'patronymic' => 'Patronymic',
            'display_name' => 'Display Name',
            'phone' => '79991112233',
            'status_id' => UserStatus::active,
            'email' => 'email@test.com',
            'email_confirmation_need' => false,
            'username' => 'Username',
            'clear_password' => false,
            'new_password' => 'password',
        ]);

        $this->assertEquals('Lastname', $vdto->lastname);
        $this->assertEquals('Firstname', $vdto->firstname);
        $this->assertEquals('Patronymic', $vdto->patronymic);
        $this->assertEquals('Display Name', $vdto->display_name);
        $this->assertEquals('79991112233', $vdto->phone);
        $this->assertEquals(UserStatus::active, $vdto->status_id);
        $this->assertEquals('email@test.com', $vdto->email);
        $this->assertFalse($vdto->email_confirmation_need);
        $this->assertEquals('Username', $vdto->username);
        $this->assertFalse($vdto->clear_password);
        $this->assertEquals('password', $vdto->new_password);
    }

    public function test_vdto_titles(): void
    {
        $vdto = new UserVDTO();
        $this->assertLocalizations([
            'lastname' => 'users/user.field_lastname',
            'firstname' => 'users/user.field_firstname',
            'patronymic' => 'users/user.field_patronymic',
            'display_name' => 'users/user.field_display_name',
            'username' => 'users/user.field_username',
            'phone' => 'users/user.field_phone',
            'status_id' => 'users/user.field_status_id',
            'new_password' => 'users/user.field_new_password',
            'clear_password' => 'users/user.field_clear_password',
            'email' => 'users/user.field_email',
            'email_confirmation_need' => 'users/user.field_email_confirmation_need',
        ], [$vdto, 'getTitles']);
        $this->assertLocalizations(['lastname' => 'users/user.field_lastname'], [$vdto, 'getTitles'], ['lastname']);
        $this->assertEquals([], $vdto->getTitles(['not_exists']));
    }

    public function test_vdto_validation_messages(): void
    {
        $vdto = new UserVDTO();
        $this->assertLocalizations([
            'phone.size' => 'users/user.validation_phone_size',
            'username.unique' => 'users/user.validation_username_unique',
            'email.unique' => 'users/user.validation_email_unique',
            'phone.unique' => 'users/user.validation_phone_unique',
        ], [$vdto, 'getValidationMessages']);
        $this->assertLocalizations(['email.unique' => 'users/user.validation_email_unique',], [$vdto, 'getValidationMessages'], ['email']);
        $this->assertEquals([], $vdto->getValidationMessages(['not_exists']));
    }

    public function test_vdto_validation_rules(): void
    {
        $vdto = new UserVDTO();
        $this->assertEquals([
            'lastname' => 'required_without:display_name',
            'firstname' => 'required',
            'patronymic' => 'nullable',
            'display_name' => 'required_without:lastname',
            'phone' => 'nullable|string|size:11',
            'status_id' => 'required|exists:user_statuses,id',
            'email' => 'required_without:username|email|nullable',
            'email_confirmation_need' => 'nullable',
            'username' => 'required_without:lastname,email',
            'clear_password' => 'nullable',
            'new_password' => 'nullable|min:6',
        ], $vdto->getValidationRules());
        $this->assertEquals(['phone' => 'nullable|string|size:11'], $vdto->getValidationRules(['phone']));
        $this->assertEquals([], $vdto->getValidationRules(['not_exists']));
    }

    public function test_vdto_validate_fails(): void
    {
        $vdto = new UserVDTO();

        $errors = $vdto->validate();
        $this->assertArrayHasKey('lastname', $errors);
        $this->assertArrayHasKey('firstname', $errors);
        $this->assertArrayHasKey('display_name', $errors);
        $this->assertArrayHasKey('status_id', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('username', $errors);
        // TODO add validation cases
    }

    public function test_vdto_validate_success(): void
    {
        $vdto = new UserVDTO([
            'lastname' => 'Lastname',
            'firstname' => 'Firstname',
            'patronymic' => 'Patronymic',
            'display_name' => 'Display Name',
            'phone' => '79991112233',
            'status_id' => UserStatus::active,
            'email' => 'email@test.com',
            'email_confirmation_need' => false,
            'username' => 'Username',
            'clear_password' => false,
            'new_password' => 'password',
        ]);

        $this->assertNull($vdto->validate());

        $vdto = new UserVDTO();
        $this->assertNull($vdto->validate(['patronymic']));
    }
}
