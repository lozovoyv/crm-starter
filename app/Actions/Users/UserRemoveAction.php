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

namespace App\Actions\Users;

use App\Actions\Action;
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChange;
use App\Models\Users\User;
use App\Utils\Casting;
use Illuminate\Database\QueryException;

class UserRemoveAction extends Action
{
    /**
     * @throws ModelDeleteBlockedException
     */
    public function execute(User $user): void
    {
        try {
            $changes = [
                new HistoryChange(['parameter' => 'lastname', 'type' => Casting::string, 'old' => $user->lastname, 'new' => null]),
                new HistoryChange(['parameter' => 'firstname', 'type' => Casting::string, 'old' => $user->firstname, 'new' => null]),
                new HistoryChange(['parameter' => 'patronymic', 'type' => Casting::string, 'old' => $user->patronymic, 'new' => null]),
                new HistoryChange(['parameter' => 'display_name', 'type' => Casting::string, 'old' => $user->display_name, 'new' => null]),
                new HistoryChange(['parameter' => 'email', 'type' => Casting::string, 'old' => $user->email, 'new' => null]),
                new HistoryChange(['parameter' => 'phone', 'type' => Casting::string, 'old' => $user->phone, 'new' => null]),
                new HistoryChange(['parameter' => 'username', 'type' => Casting::string, 'old' => $user->username, 'new' => null]),
            ];

            $user
                ->addHistory(HistoryAction::user_deleted, $this->current?->positionId())
                ->addChanges($changes);

            $user->delete();

        } catch (QueryException) {
            throw new ModelDeleteBlockedException('Невозможно удалить учётную запись. Она задействована в системе');
        }
    }
}
