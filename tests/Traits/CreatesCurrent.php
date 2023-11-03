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
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;

trait CreatesCurrent
{
    use CreatesUser, CreatesPosition;

    /**
     * Creates the position.
     *
     * @param int $type
     * @param array $permissions
     *
     * @return Current
     */
    public function initCurrent(int $type, array $permissions = []): Current
    {
        $user = $this->createUser('test');
        $position = $this->createPosition($user, $type, $permissions);

        $request = Request::create('/');
        $request->setLaravelSession(new Store('array', new ArraySessionHandler(10)));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->session()->put('position_id', $position->id);

        return Current::init($request, true);
    }
}
