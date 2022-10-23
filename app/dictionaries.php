<?php

use App\Models\Positions\PositionType;
use App\Models\Users\UserStatus;

return [
    'user_statuses' => [
        'class' => UserStatus::class,
        'allow' => [
            PositionType::staff => ['system.users'],
        ]
    ],
];
