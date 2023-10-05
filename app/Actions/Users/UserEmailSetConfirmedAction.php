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
use InvalidArgumentException;

class UserEmailSetConfirmedAction extends Action
{
    /**
     * Apply new confirmed email.
     *
     * @param User $user
     * @param string $email
     *
     * @return void
     */
    public function execute(User $user, string $email): void
    {
        if (User::query()->where('email', $email)->count()) {
            throw new InvalidArgumentException('Адрес электронной почты ' . $email . ' уже занят');
        }
        $change = $user->setAttributeWithChanges('email', $email, Casting::string);
        $user->save();
        $user->addHistory(HistoryAction::user_email_verified, null)->addChanges([$change]);
    }
}
