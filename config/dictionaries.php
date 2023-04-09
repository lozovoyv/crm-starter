<?php
declare(strict_types=1);

use App\Dictionaries\PermissionRoleDictionary;
use App\Dictionaries\PositionStatusDictionary;
use App\Dictionaries\UserDictionary;
use App\Dictionaries\UserStatusDictionary;
use App\Models\Positions\PositionType;

/**
 * Dictionaries configurations.
 *
 * Record format:
 *
 * Key is dictionary alias for accessing.
 * Record contains next options:
 * — class - dictionary processor class, @see \App\Dictionaries\Dictionary
 * — view - set of view permissions assigned to position type.
 * — edit - set of edit permissions assigned to position type.
 */
return [
    'users' => [
        'class' => UserDictionary::class,
        'view' => [PositionType::admin => true, PositionType::staff => ['system.staff.change']],
        'edit' => false,
    ],
    'user_statuses' => [
        'class' => UserStatusDictionary::class,
        'view' => [PositionType::admin => true, PositionType::staff => ['system.users.change']],
        'edit' => false,
    ],
    'position_statuses' => [
        'class' => PositionStatusDictionary::class,
        'view' => [PositionType::admin => true, PositionType::staff => ['system.staff.change']],
        'edit' => false,
    ],
    'roles' => [
        'class' => PermissionRoleDictionary::class,
        'view' => [PositionType::admin => true, PositionType::staff => ['system.staff.change']],
        'edit' => false,
    ],
    'test' => [
        'class' => \App\Dictionaries\TestDictionary::class,
        'view' => true,
        'edit' => true,
    ],
];
