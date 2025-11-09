<?php

namespace App\Providers;

use App\Events\LhpEvent;
use App\Events\TindakLanjutTemuanEvent;
use App\Listeners\SendLhpNotification;
use App\Listeners\SendTindakLanjutTemuanNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LhpEvent::class => [
            SendLhpNotification::class,
        ],
        TindakLanjutTemuanEvent::class => [
            SendTindakLanjutTemuanNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
