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

namespace App\Models\History;

use App\Models\Model;
use Database\Seeders\Seeders\HistoryActionsSeeder;

/**
 * @property int $id
 * @property string $name
 *
 * @see HistoryActionsSeeder
 */
class HistoryAction extends Model
{
    /** SYSTEM (1-200) */

    /** DICTIONARIES (151-200) */
    public const dictionary_item_created = 151;
    public const dictionary_item_edited = 155;
    public const dictionary_item_deleted = 159;
    public const dictionary_item_activated = 161;
    public const dictionary_item_deactivated = 165;

    /** PERMISSIONS AND PERMISSION GROUPS (201-300) */
    public const permission_group_created = 201;
    public const permission_group_edited = 211;
    public const permission_group_deleted = 221;
    public const permission_group_activated = 231;
    public const permission_group_deactivated = 241;

    /** USERS (301-400) */
    public const user_created = 301;
    public const user_edited = 311;
    public const user_password_set = 312;
    public const user_password_changed = 313;
    public const user_password_cleared = 314;
    public const user_deleted = 321;
    public const user_activated = 331;
    public const user_deactivated = 341;
    public const user_staff_attached = 361;
    public const user_email_set = 371;
    public const user_email_changed = 372;
    public const user_email_cleared = 373;
    public const user_email_verification_sent = 376;
    public const user_email_verified = 377;

    /** POSITIONS (401-500) */
    public const staff_position_created = 401;
    public const staff_position_edited = 411;
    public const staff_position_deleted = 421;
    public const staff_position_activated = 431;
    public const staff_position_deactivated = 432;

}
