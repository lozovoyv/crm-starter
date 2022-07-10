<?php

namespace App\Console\Commands;

use App\Console\Generator\Traits\CreatesDictionary;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeDictionaryCommand extends GeneratorCommand
{
    use CreatesDictionary;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:dictionary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dictionary migration and model class';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['size', 's', InputOption::VALUE_OPTIONAL, 'Dictionary size. Available: tiny, small, int, big. Default: tiny'],
            ['migrate', 'm', InputOption::VALUE_NONE, 'Run migration for this dictionary immediately'],
            ['check', 'c', InputOption::VALUE_NONE, 'Check dictionary could be created'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['dictionary', InputArgument::REQUIRED, 'The name of dictionary'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('dictionary');
        $size = $this->hasOption('size') ? $this->option('size') : null;

        if ($errors = $this->canCreateDictionary($name)) {
            foreach ($errors as $error) {
                $this->error($error);
            }
            return 10;
        }

        if (!empty($this->option('check'))) {
            $this->info('Dictionary ' . $name . ' could be created');
            return 0;
        }

        $this->info('Creating dictionary ' . $name);

        // Create migration
        $migrationFilename = $this->createDictionaryMigration($name, $size);
        $this->info('Migration file created: ' . $migrationFilename);

        // Create model
        $result = $this->createDictionaryModel($name);
        $this->info('Class created: ' . $result);

        // Run migration
        if (!empty($this->option('migrate'))) {
            $this->migrateDictionary($migrationFilename);
        }

        return 0;
    }
}
