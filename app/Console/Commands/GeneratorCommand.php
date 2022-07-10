<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

abstract class GeneratorCommand extends Command
{
    /**
     * Size definition for DB
     *
     * @param string|null $size
     *
     * @return  string
     */
    protected function getIndexSize(?string $size): string
    {
        return match ($size) {
            'small' => 'unsignedSmallInteger',
            'int' => 'unsignedInteger',
            'big' => 'unsignedBigInteger',
            default => 'unsignedTinyInteger',
        };
    }

    /**
     * Generate table name for dictionary.
     *
     * @param string $name
     *
     * @return  string
     */
    protected function getTableName(string $name): string
    {
        return Str::snake(Str::pluralStudly($name));
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function getStub(string $stub): string
    {
        return file_get_contents(__DIR__ . '/../Generator/stubs/' . $stub);
    }

    /**
     * Replace the tag for the given stub.
     *
     * @param string $search
     * @param string $replace
     * @param string $stub
     *
     * @return string
     */
    protected function replace(string $search, string $replace, string $stub): string
    {
        return str_replace(["{{ $search }}", "{{$search}}"], $replace, $stub);
    }
}
