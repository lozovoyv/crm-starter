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
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChanges;
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
                new HistoryChanges(['parameter' => 'lastname', 'type' => Casting::string, 'old' => $user->lastname, 'new' => null]),
                new HistoryChanges(['parameter' => 'firstname', 'type' => Casting::string, 'old' => $user->firstname, 'new' => null]),
                new HistoryChanges(['parameter' => 'patronymic', 'type' => Casting::string, 'old' => $user->patronymic, 'new' => null]),
                new HistoryChanges(['parameter' => 'display_name', 'type' => Casting::string, 'old' => $user->display_name, 'new' => null]),
                new HistoryChanges(['parameter' => 'email', 'type' => Casting::string, 'old' => $user->email, 'new' => null]),
                new HistoryChanges(['parameter' => 'phone', 'type' => Casting::string, 'old' => $user->phone, 'new' => null]),
                new HistoryChanges(['parameter' => 'username', 'type' => Casting::string, 'old' => $user->username, 'new' => null]),
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