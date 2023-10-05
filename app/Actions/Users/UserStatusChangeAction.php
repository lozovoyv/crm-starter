<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Actions\Users;

use App\Actions\Action;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\VDTO\UserVDTO;

class UserStatusChangeAction extends Action
{
    /**
     * Change user status.
     *
     * @param User $user
     * @param UserVDTO $vdto
     *
     * @return void
     */
    public function execute(User $user, UserVDTO $vdto): void
    {
        if ($vdto->status_id !== $user->status_id) {
            $user->setStatus($vdto->status_id, true);

            $action = match ($user->status_id) {
                UserStatus::active => HistoryAction::user_activated,
                UserStatus::blocked => HistoryAction::user_deactivated,
            };

            $user->addHistory($action, $this->current?->positionId());
        }
    }
}
