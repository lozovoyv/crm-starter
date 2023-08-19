<?php
declare(strict_types=1);

namespace App\Dictionaries\Base;

use App\Current;
use Carbon\Carbon;

interface DictionaryInterface
{
    public static function view(Current $current, ?Carbon $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewContainerInterface;
}
