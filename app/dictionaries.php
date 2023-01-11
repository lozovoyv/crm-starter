<?php

use App\Models\Permissions\PermissionRolesDictionary;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;
use App\Models\Users\UsersDictionary;
use App\Models\Users\UserStatus;

return [
    'users' => [
        'class' => UsersDictionary::class,
        'allow' => [
            PositionType::staff => ['system.staff.change'],
        ]
    ],
    'user_statuses' => [
        'class' => UserStatus::class,
        'allow' => [
            PositionType::staff => ['system.users.change'],
        ]
    ],
    'position_statuses' => [
        'class' => PositionStatus::class,
        'allow' => [
            PositionType::staff => ['system.staff.change'],
        ]
    ],
    'roles' => [
        'class' => PermissionRolesDictionary::class,
        'allow' => [
            PositionType::staff => ['system.staff.change'],
        ]
    ],
];
