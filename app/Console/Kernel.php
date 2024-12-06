<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\StoreSaleController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\AdminRmaItemsController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\AdminGachaItemsController;
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
        $schedule->call(function(){
            $itemMaster = new AdminItemsController();
            $itemMaster->getDimfsData();
            $gashapon = new AdminGachaItemsController();
            $gashapon->getDimfsGachaItemsData();
            $rmaItemMaster = new AdminRmaItemsController();
            $rmaItemMaster->getDimfsRmaItemsData();
        })->hourly();

        
        $schedule->call(function(){
            $datefrom = Carbon::now()->subHours(5)->format('Ymd'); 
            $dateto = Carbon::now()->subHours(1)->format('Ymd');

            $request = new Request([
                'datefrom' => $datefrom,
                'dateto' => $dateto,
            ]);

            $storeSale = new StoreSaleController();
            $storeSale->StoresSalesFromPosEtp($request);
        })->dailyAt('23:00:00');

        $schedule->call(function(){

            $datefrom = Carbon::now()->subHours(5)->format('Ymd'); 
            $dateto = Carbon::now()->subDays(1)->format('Ymd');

            $request = new Request([
                'datefrom' => $datefrom,
                'dateto' => $dateto,
            ]);

            $storeInventory = new StoreInventoryController();
            $storeInventory->StoresInventoryFromPosEtp($request);
            
        })->dailyAt('00:00:00');

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
