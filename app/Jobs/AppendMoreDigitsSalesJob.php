<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\StoreTestExportBatches;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DigitsSale;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Storage;
use Excel;
use CRUDBooster;

class AppendMoreDigitsSalesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $userReport;
    private $chunkIndex;
    private $chunkSize;
    private $folder;
    private $filters;
    private $filename;
    public function __construct($chunkIndex, $chunkSize, $folder, $filter, $filename) {
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $this->chunkIndex = $chunkIndex;
        $this->chunkSize = $chunkSize;
        $this->folder = $folder;
        $this->filters = $filter;
    }

    public function handle()
    {
        $digitsSales = DigitsSale::filterForReport(DigitsSale::generateReport(), $filters)
            ->skip($this->chunkIndex * $this->chunkSize)
            ->take($this->chunkSize)
            ->get()
            ->map(function ($sale) {
                //MAP USER REPORT PRIVILEGE
                $sales = explode("`,`",$this->userReport->report_query);
                $salesReport = [];
                foreach($sales as $key => $value) {
                    array_push($salesReport,$sale->$value);
                }
                return $salesReport;
            });

        $file = storage_path("app/{$this->folder}/ExportDigitsSales.csv");
        $open = fopen($file, 'a+');
        foreach ($digitsSales as $sale) {
            fputcsv($open, $sale);
        }
        fclose($open);
    }
}
