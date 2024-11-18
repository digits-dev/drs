<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\StoreSaleController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\AdminGachaItemsController;
use App\Http\Controllers\AdminRmaItemsController;
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
        $schedule->call(function(){
            $itemMaster = new AdminItemsController();
            $itemMaster->getDimfsData();
            $gashapon = new AdminGachaItemsController();
            $gashapon->getDimfsGachaItemsData();
            $rmaItemMaster = new AdminRmaItemsController();
            $rmaItemMaster->getDimfsRmaItemsData();
        })->hourly();

        $schedule->call(function(){
            $storeInventory = new StoreInventoryController();
            $storeInventory->StoresInventoryFromPosEtp();
            $storeSale = new StoreSaleController();
            $storeSale->StoresSalesFromPosEtp();
            
        })->dailyAt('23:00:00');
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
