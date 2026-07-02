<?php

namespace App\Console;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\BaseController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DatabaseBackUp',
        'App\Console\Commands\SyncToOnlineDatabase',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Daily database backup
        $schedule->command('database:backup');

        // Push local records to online DB every 30 minutes
        // Only runs if DB_SYNC_ENABLED=true in .env
        $schedule->command('db:sync-online')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping(10)          // skip if previous run still active (10 min lock)
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/db_sync.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
