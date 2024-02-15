<?php

namespace App\Exports;

use App\Models\DigitsSale;
use App\Models\DigitsSalesReport;
use App\Models\ReportPrivilege;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class DigitsSalesUploadBatchExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $userReport;
    private $batch;

    public function __construct($batch) {
        $this->batch = $batch;
        $this->userReport = ReportPrivilege::myReport(2,3);
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
}