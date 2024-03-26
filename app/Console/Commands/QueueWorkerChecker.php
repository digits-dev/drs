<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueueWorkerChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and start queue worker if not running';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if "php artisan queue:work" is already running
        $output = [];
        exec('pgrep -f "php artisan queue:work"', $output);
        if (!empty($output)) {
            $this->info('Queue worker is already running. Doing nothing.');
        } else {
            // If not running, start the queue worker
            $this->info('Queue worker not running. Starting...');
            exec('/opt/cpanel/ea-php74/root/usr/bin/php artisan queue:work --timeout=600');
        }
    }
}
