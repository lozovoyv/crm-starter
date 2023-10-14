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
use App\Utils\Casting;
use App\VDTO\UserVDTO;

class UserUpdateAction extends Action
{
    /**
     * Update user data.
     *
     * @param User $user
     * @param UserVDTO $vdto
     *
     * @return void
     */
    public function execute(User $user, UserVDTO $vdto): void
    {
        $changes = [];

        $changes[] = $user->setAttributeWithChanges('lastname', $vdto->lastname, Casting::string);
        $changes[] = $user->setAttributeWithChanges('firstname', $vdto->firstname, Casting::string);
        $changes[] = $user->setAttributeWithChanges('patronymic', $vdto->patronymic, Casting::string);
        $changes[] = $user->setAttributeWithChanges('display_name', $vdto->display_name, Casting::string);
        $changes[] = $user->setAttributeWithChanges('username', $vdto->username, Casting::string);
        $changes[] = $user->setAttributeWithChanges('phone', $vdto->phone, Casting::string);

        $changes = array_filter($changes);

        if (!empty($changes)) {
            $user->save();
            $user
                ->addHistory($user->wasRecentlyCreated ? HistoryAction::user_created : HistoryAction::user_edited, $this->current?->positionId())
                ->addChanges($changes);
        }
    }
}
