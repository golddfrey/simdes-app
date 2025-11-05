<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * EventServiceProvider
 *
 * Mengatur event–listener sistem Laravel.
 * Untuk sekarang biarkan kosong (default).
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Event listener mapping.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        //
    ];

    /**
     * Tentukan apakah event akan auto-discover.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
