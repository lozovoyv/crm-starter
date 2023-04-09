<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     * @noinspection PhpUnused
     */
    protected $listen = [
        //Event::class => [
        //    Listener::class,
        //],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function boot(): void
    {
        //
    }
}
