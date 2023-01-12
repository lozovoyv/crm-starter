<?php

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

    /** PERMISSIONS ADN ROLES (201-300) */
    public const permission_role_created = 201;
    public const permission_role_edited = 211;
    public const permission_role_deleted = 221;
    public const permission_role_activated = 231;
    public const permission_role_deactivated = 241;

    /** USERS (301-400) */
    public const user_created = 301;
    public const user_edited = 311;
    public const user_password_set = 312;
    public const user_password_changed = 313;
    public const user_password_cleared = 314;
    public const user_deleted = 321;
    public const user_activated = 331;
    public const user_deactivated = 341;

    /** POSITIONS (401-500) */
    public const staff_position_created = 401;
}
