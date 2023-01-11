<?php

namespace App\Models;

use App\Models\Permissions\PermissionRole;
use App\Models\Positions\Position;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Virtual dictionary for history record scopes
 *
 * Name limit is 30 characters
 */
class EntryScope
{
    public const user = 'user';
    public const position = 'position';
    public const role = 'role';

    public static function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            self::user => User::class,
            self::position => Position::class,
            self::role => PermissionRole::class,
        ]);
    }
}
