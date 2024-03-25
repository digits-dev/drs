<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBatchImportStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $batch;
    public $excel_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batch, $excel_path)
    {
        $this->batch = $batch;
        $this->excel_path = $excel_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($batch->status !== 'FINAL') {
            $this->batch->status = 'FILE GENERATED';
        }
        $this->batch->errors = null;
        $this->batch->generated_file_path = $this->excel_path;
        $this->batch->save();
    }
}
