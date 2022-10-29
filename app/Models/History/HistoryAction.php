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

    /** POSITIONS (401-500) */
    public const staff_position_created = 401;
}
