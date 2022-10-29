<?php

namespace App\Models\History\Formatters;

use App\Models\History\HistoryChanges;

class UserChangesFormatter implements FormatterInterface
{
    public static function format(HistoryChanges $changes): array
    {
        $result = [
            'parameter' => $changes->parameter,
            'old' => $changes->old,
            'new' => $changes->new,
        ];

        switch ($result['parameter']) {
            case 'lastname':
                $result['parameter'] = 'Фамилия';
                break;
            case 'firstname':
                $result['parameter'] = 'Имя';
                break;
            case 'patronymic':
                $result['parameter'] = 'Отчество';
                break;
            case 'email':
                $result['parameter'] = 'Email';
                break;
            case 'phone':
                $result['parameter'] = 'Телефон';
                break;
            case 'username':
                $result['parameter'] = 'Имя пользователя';
                break;
        }

        return $result;
    }
}
