<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Load migrations from all subdirectories of `database/migrations`
            $directories = array_filter(glob(database_path('migrations') . '/*'), 'is_dir');
            $this->loadMigrationsFrom($directories);
        }
    }
}
