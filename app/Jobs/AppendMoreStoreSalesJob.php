<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\StoreTestExportBatches;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\StoreSale;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Storage;
use Excel;
use CRUDBooster;

class AppendMoreStoreSalesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $userReport;
    private $chunkIndex;
    private $chunkSize;
    private $folder;
    private $filters;
    public function __construct($chunkIndex, $chunkSize, $folder, $filter) {
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $this->chunkIndex = $chunkIndex;
        $this->chunkSize = $chunkSize;
        $this->folder = $folder;
        $this->filters = $filter;
    }

    public function handle()
    {
        $storeSales = StoreSale::filterForReport(StoreSale::generateReport(), $this->fllters)
            ->where('is_final', 1)
            ->skip($this->chunkIndex * $this->chunkSize)
            ->take($this->chunkSize)
            ->get()
            ->map(function ($sale) {
                //MAP USER REPORT PRIVILEGE
                $sales = explode("`,`",$this->userReport->report_query);
                $salesReport = [];
                foreach ($sales as $key => $value) {
                    array_push($salesReport,$sale->$value);
                }
                return $salesReport;
            });

        $file = storage_path("app/{$this->folder}/storeSales.csv");
        $open = fopen($file, 'a+');
        foreach ($storeSales as $sale) {
            fputcsv($open, $sale);
        }
        fclose($open);
    }
}
