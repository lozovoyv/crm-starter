<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * @noinspection PhpUnused
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
