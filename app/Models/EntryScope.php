<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Permissions\PermissionGroup;
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
    public const permission_group = 'permission_group';
    public const dictionary = 'dictionary';

    public static function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            self::user => User::class,
            self::position => Position::class,
            self::permission_group => PermissionGroup::class,
        ]);
    }
}
