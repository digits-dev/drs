<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\QueueWorkerChecker::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call('\App\Http\Controllers\AdminItemsController@getDimfsData')->everyMinute();
        $schedule->call('\App\Http\Controllers\AdminGachaItemsController@getDimfsGachaItemsData')->hourly();
        $schedule->call('\App\Http\Controllers\AdminRmaItemsController@getDimfsRmaItemsData')->everyMinute();
        
        $schedule->command('queue:check')->everyMinute()->withoutOverlapping();
        
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
