<?php
declare(strict_types=1);

namespace App\Dictionaries;

interface DictionaryInterface
{
    public static function view(bool $isEditable, ?string $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewInterface;
}
