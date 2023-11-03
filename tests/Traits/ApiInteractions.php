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

namespace Tests\Traits;

use App\Current;
use App\Models\Permissions\Permission;
use Laravel\Sanctum\Sanctum;

trait ApiInteractions
{
    use CreatesUser, CreatesPosition;

    protected function apiActingAs(?int $positionType, array $permissions = [], array $session = []): self
    {
        Current::unset();

        $this
            ->withSession($session)
            ->withHeader('Accept', 'application/json')
            ->withHeader('origin', '127.0.0.1');

        if ($positionType === null) {
            return $this;
        }

        $user = $this->createUser('testing', 'user');
        $position = $this->createPosition($user, $positionType);
        $position->permissions()->sync(Permission::query()->whereIn('key', $permissions)->pluck('id')->toArray());
        Sanctum::actingAs($user);

        return $this->actingAs($user);
    }
}
