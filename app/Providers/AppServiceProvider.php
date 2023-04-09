<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\EntryScope;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function boot(): void
    {
        EntryScope::enforceMorphMap();

        if ($this->app->runningInConsole()) {
            // Load migrations from all subdirectories of `database/migrations`
            $directories = array_filter(glob(database_path('migrations') . '/*'), 'is_dir');
            $this->loadMigrationsFrom($directories);
        }
    }
}
