<?php

namespace App\Exports;

use App\Models\ReportPrivilege;
use App\Models\DigitsSalesReport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class DigitsSalesExport implements FromQuery, WithHeadings, WithMapping
{

    use Exportable;
    private $userReport;

    public function __construct() {
        $this->userReport = ReportPrivilege::myReport(2,1);
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
        $salesReport = DigitsSalesReport::selectRaw("`".$this->userReport->report_query."`")->searchFilter(request()->all());
       
        return $salesReport;
    }
}