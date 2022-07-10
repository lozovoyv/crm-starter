<?php

namespace App\Console\Generator\Traits;

use App\Console\Commands\GeneratorCommand;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @mixin GeneratorCommand
 */
trait CreatesDictionary
{
    /**
     * Generate table name for dictionary.
     *
     * @param string $name
     *
     * @return  string
     */
    protected function getDictionaryTableName(string $name): string
    {
        return 'dictionary_' . $this->getTableName($name);
    }

    /**
     * Generate migration filename for dictionary.
     *
     * @param string $name
     * @param bool $withPath
     * @param bool $mask
     *
     * @return  string
     */
    protected function getDictionaryMigrationFilename(string $name, bool $withPath = true, bool $mask = false): string
    {
        $files = array_filter(glob(database_path('migrations/dictionaries/*')), 'is_file');
        $count = count($files) + 1;

        $prefix = $mask ? '*' : str_pad($count, 4, '0', STR_PAD_LEFT);

        $filename = '0000_00_00_' . $prefix . '00_create_' . $this->getDictionaryTableName($name) . '_table.php';

        if (!$withPath) {
            return $filename;
        }

        return database_path('migrations/dictionaries') . '/' . $filename;
    }

    /**
     * Generate class name for migration.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getDictionaryMigrationClassName(string $name): string
    {
        return 'Create' . Str::studly($this->getDictionaryTableName($name)) . 'Table';
    }

    /**
     * Generate class name for dictionary.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getDictionaryModelClassName(string $name): string
    {
        return 'App\\Models\\Dictionaries\\' . $name;
    }

    /**
     * Generate class name for dictionary.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getDictionaryModelFilename(string $name): string
    {
        return app_path('Models/Dictionaries') . '/' . $name . '.php';
    }

    /**
     * Check dictionary could be created.
     *
     * @param string $name
     *
     * @return  array|null
     */
    protected function canCreateDictionary(string $name): ?array
    {
        $errors = [];

        // Check table exists
        if (Schema::hasTable($tableName = $this->getDictionaryTableName($name))) {
            $errors[] = "Table [$tableName] exists.";
        }
        // Check migration file exists
        $mask = $this->getDictionaryMigrationFilename($name, true, true);
        $files = glob($mask);
        if (!empty($files)) {
            $errors[] = 'Migration file [' . $files[0] . '] exists.';
        }
        // Check migration class exists
        if (class_exists($className = $this->getDictionaryMigrationClassName($name))) {
            $errors[] = "Migration class [$className] exists.";
        }
        // Check model exists
        if (class_exists($className = $this->getDictionaryModelClassName($name))) {
            $errors[] = "Class [$className] exists.";
        }
        // Check model file exists
        if (file_exists($fileName = $this->getDictionaryModelFilename($name))) {
            $errors[] = "File [$fileName] exists.";
        }

        return empty($errors) ? null : $errors;
    }


    /**
     * Create a migration file for the dictionary.
     *
     * @param string $name
     * @param string|null $size
     *
     * @return string
     */
    protected function createDictionaryMigration(string $name, ?string $size): string
    {
        $stub = $this->getStub('dictionary.migration.stub');

        $stub = $this->replace('dictionary_migration_class', $this->getDictionaryMigrationClassName($name), $stub);
        $stub = $this->replace('dictionary_table', $this->getDictionaryTableName($name), $stub);
        $stub = $this->replace('index_size', $this->getIndexSize($size), $stub);
        $filename = $this->getDictionaryMigrationFilename($name);
        file_put_contents($filename, $stub);

        return $filename;
    }

    /**
     * Create a model file for the dictionary.
     *
     * @param string $name
     *
     * @return string
     */
    protected function createDictionaryModel(string $name): string
    {
        $stub = $this->getStub('dictionary.model.stub');
        $stub = $this->replace('dictionary_model_class', $name, $stub);
        $stub = $this->replace('dictionary_table', $this->getDictionaryTableName($name), $stub);
        file_put_contents($this->getDictionaryModelFilename($name), $stub);

        return $this->getDictionaryModelClassName($name);
    }

    /**
     * Run migrate command for this dictionary.
     *
     * @param string $filename
     *
     * @return  void
     */
    protected function migrateDictionary(string $filename): void
    {
        $this->call('migrate', [
            '--path' => 'database/migrations/dictionaries/' . $filename,
        ]);
    }
}
