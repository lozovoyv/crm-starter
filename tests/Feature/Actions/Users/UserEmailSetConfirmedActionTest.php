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

namespace Tests\Feature\Actions\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesPosition;
use Tests\Traits\CreatesUser;

class UserEmailSetConfirmedActionTest extends TestCase
{
    use CreatesUser, CreatesPosition, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

//    public function test_execute():void
//    {
//
//    }
}
