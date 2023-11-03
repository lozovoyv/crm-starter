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

namespace Tests\Feature\API\Auth;

use Tests\TestCase;

class AuthConfigTest extends TestCase
{
    public function test_api_auth_config(): void
    {
        config()->set('auth.registration_enabled', true);
        config()->set('auth.reset_password_enabled', true);
        $response = $this->apiActingAs(null)->get('/api/auth/config');

        $response->assertJson(['data' => ['registration_enabled' => true, 'reset_password_enabled' => true]]);

        config()->set('auth.registration_enabled', false);
        config()->set('auth.reset_password_enabled', false);
        $response = $this->apiActingAs(null)->get('/api/auth/config');

        $response->assertJson(['data' => ['registration_enabled' => false, 'reset_password_enabled' => false]]);

    }
}
