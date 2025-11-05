<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * App\Console\Kernel
 *
 * Kernel konsol standar Laravel. Daftarkan command-artisan khusus
 * dan jadwalkan tugas di sini bila perlu.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        // \App\Console\Commands\YourCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // contoh:
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        // require base_path('routes/console.php'); // Uncomment if you use routes/console.php
        if (file_exists($path = base_path('routes/console.php'))) {
            require $path;
        }
    }
}
