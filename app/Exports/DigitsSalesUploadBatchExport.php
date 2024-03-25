<?php

namespace App\Exports;

use App\Models\DigitsSale;
use App\Models\DigitsSalesReport;
use App\Models\DigitsSalesUpload;
use App\Models\ReportPrivilege;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;

class DigitsSalesUploadBatchExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithCustomChunkSize
{
    use Exportable;
    private $userReport;
    private $batch;

    public function __construct($batch) {
        $this->batch = $batch;
        DigitsSalesUpload::where('batch', $batch)->update(['status' => 'GENERATING FILE']);
        $this->userReport = ReportPrivilege::myReport(2,CRUDBooster::myPrivilegeId());
    }

    public function headings(): array {
        return explode(",",$this->userReport->report_header);

    }

    public function map($item): array {

        $sales = explode("`,`",$this->userReport->report_query);
        $salesReport = [];

        foreach ($sales as $key => $value) {
            array_push($salesReport,$item->$value);
        }

        return $salesReport;
    }

    public function query()
    {
        return DigitsSale::generateReport()
            ->where('batch_number', $this->batch)
            ->orderBy('reference_number', 'ASC');
    }

    public function failed($exception) : void {
        $error_message = $exception->getMessage();
        $store_sales_upload = DigitsSalesUpload::where('batch', $this->batch)->first();
        $store_sales_upload->status = 'FAILED TO GENERATE FILE';
        $store_sales_upload->save();
        $store_sales_upload->appendNewError($error_message);
    }

    public function chunkSize(): int {
        return 1000;
    }
}