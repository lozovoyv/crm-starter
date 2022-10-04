<?php

namespace App\Models\History;

use App\Models\Model;

/**
 * @property int $id
 * @property string $name
 */
class HistoryAction extends Model
{
/** SYSTEM (1-200) */

/** PERMISSIONS ADN ROLES (201-301) */
    public const permission_role_created = 201;
    public const permission_role_edited = 211;
    public const permission_role_deleted = 221;
    public const permission_role_activated = 231;
    public const permission_role_deactivated = 241;
}
