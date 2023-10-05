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
use App\VDTO\UserVDTO;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;

class UserPasswordChangeAction extends Action
{
    /**
     * Change user password.
     *
     * @param User $user
     * @param UserVDTO $vdto
     *
     * @return void
     */
    public function execute(User $user, UserVDTO $vdto): void
    {
        if ($vdto->clear_password || $vdto->new_password === null) {
            if (!empty($user->password)) {
                $user->password = null;
                $user->save();
                $user->addHistory(HistoryAction::user_password_cleared, $this->current?->positionId());
            }
        } else {
            $action = match (true) {
                empty($user->password) => HistoryAction::user_password_set,
                default => HistoryAction::user_password_changed,
            };
            $user->password = Hash::make($vdto->new_password);
            $user->save();
            $user->addHistory($action, $this->current?->positionId());
        }
    }
}
