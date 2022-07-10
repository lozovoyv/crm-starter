<?php

namespace App\Exceptions\User;

use App\Exceptions\WrongStatusException;

class WrongUserStatusException extends WrongStatusException
{
    public function __construct()
    {
        parent::__construct('Неверный статус');
    }
}
