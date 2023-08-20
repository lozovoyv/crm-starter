<?php
declare(strict_types=1);

namespace Tests\Traits;

use App\Current;
use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Users\User;
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
