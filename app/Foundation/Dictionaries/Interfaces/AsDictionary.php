<?php

namespace App\Foundation\Dictionaries\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface AsDictionary
{
    /**
     * Represent model as dictionary.
     *
     * @return  Builder
     */
    public static function asDictionary(): Builder;
}