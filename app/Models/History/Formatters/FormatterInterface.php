<?php
declare(strict_types=1);

namespace App\Models\History\Formatters;

use App\Models\History\HistoryChanges;

interface FormatterInterface
{
    public static function format(HistoryChanges $changes): array;
}
