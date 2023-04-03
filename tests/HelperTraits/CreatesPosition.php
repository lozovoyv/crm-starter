<?php
declare(strict_types=1);

namespace Tests\HelperTraits;

use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Users\User;

trait CreatesPosition
{
    /**
     * Creates the position.
     *
     * @param User $user
     * @param int $type
     *
     * @return Position
     */
    public function createPosition(User $user, int $type): Position
    {
        $position = new Position();
        $position->type_id = $type;
        $position->user_id = $user->id;
        $position->status_id = PositionStatus::active;
        $position->save();

        return $position;
    }
}
