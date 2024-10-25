<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Exports\StoreInventoryExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ExportStoreInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $toExcelContent;
    protected $excelPath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $toExcelContent, string $excelPath)
    {
        $this->toExcelContent = $toExcelContent;
        $this->excelPath = $excelPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new StoreInventoryExcel($this->toExcelContent), $this->excelPath);
    }
}
