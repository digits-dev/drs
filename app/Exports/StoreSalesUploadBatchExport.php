<?php

namespace App\Exports;

use App\Models\ReportPrivilege;
use App\Models\StoreSalesReport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class StoreSalesUploadBatchExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $userReport;
    private $batch;

    public function __construct($batch) {
        $this->batch = $batch;
        $this->userReport = ReportPrivilege::myReport(1,3);
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
        return StoreSalesReport::selectRaw("`".$this->userReport->report_query."`")
            ->orderBy('reference_number', 'ASC')
            ->where('batch_number', $this->batch);
    }
}