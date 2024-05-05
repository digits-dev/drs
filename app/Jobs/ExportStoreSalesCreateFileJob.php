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

class ExportStoreSalesCreateFileJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $userReport;
    private $chunkSize;
    private $folder;
    private $filters;
    public function __construct($chunkSize, $folder, $filter) {
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $this->chunkSize = $chunkSize;
        $this->folder = $folder;
        $this->filters = $filter;
    }

    public function handle(){

        $storeSales = StoreSale::filterForReport(StoreSale::generateReport(), $this->filters)
            ->where('is_final', 1)
            ->take($this->chunkSize)
            ->get();

        Storage::disk('local')->makeDirectory($this->folder);

        (new \Rap2hpoutre\FastExcel\FastExcel($this->salesGenerator($storeSales)))
            ->export(storage_path("app/{$this->folder}/storeSales.csv"), function ($sale) {
                $salesReport = [];
                $salesReportCon = [];
                $salesHeader = explode(",",$this->userReport->report_header);
                $salesValue = explode("`,`",$this->userReport->report_query);
                //MAP USER REPORT PRIVILEGE
                foreach($salesValue as $key => $value) {
                    $salesReport[$salesHeader[$key]] = $sale->$value;
                }
                return $salesReport;
            });
    }

    private function salesGenerator($sales){
        foreach ($sales as $sale) {
            yield $sale;
        }
    }
}



