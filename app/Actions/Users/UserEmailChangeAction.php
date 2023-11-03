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
use App\Mail\EmailChange;
use App\Models\History\HistoryAction;
use App\Models\Users\User;
use App\Models\Users\UserEmailConfirmation;
use App\Utils\Casting;
use App\VDTO\UserVDTO;
use Illuminate\Support\Facades\Mail;

class UserEmailChangeAction extends Action
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
        if ($user->email === $vdto->email) {
            return;
        }

        if (!$vdto->email_confirmation_need || $vdto->email === null) {

            $oldEmail = $user->email;
            $change = $user->setAttributeWithChanges('email', $vdto->email, Casting::string);
            $user->save();

            $action = match (true) {
                ($oldEmail === null) => HistoryAction::user_email_set,
                ($vdto->email === null) => HistoryAction::user_email_cleared,
                default => HistoryAction::user_email_changed,
            };

            $user
                ->addHistory($action, $this->current?->positionId())
                ->addChanges([$change]);

            return;
        }

        Mail::send(new EmailChange(UserEmailConfirmation::create($user, $vdto->email)));

        $user->addHistory(HistoryAction::user_email_verification_sent, $this->current?->positionId(), $vdto->email);
    }
}
