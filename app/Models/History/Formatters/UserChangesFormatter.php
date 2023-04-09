<?php
declare(strict_types=1);

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
            case 'display_name':
                $result['parameter'] = 'Отображаемое имя';
                break;
            case 'email':
                $result['parameter'] = 'Email';
                break;
            case 'phone':
                $result['parameter'] = 'Телефон';
                $result['old'] = $result['old'] ? preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1 ($2) $3-$4-$5', $result['old']) : null;
                $result['new'] = $result['new'] ? preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1 ($2) $3-$4-$5', $result['new']) : null;
                break;
            case 'username':
                $result['parameter'] = 'Имя пользователя';
                break;
        }

        return $result;
    }
}
