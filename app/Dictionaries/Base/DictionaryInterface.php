<?php
declare(strict_types=1);

namespace App\Dictionaries\Base;

use App\Current;

interface DictionaryInterface
{
    public static function view(Current $current, ?string $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewContainerInterface;
}
