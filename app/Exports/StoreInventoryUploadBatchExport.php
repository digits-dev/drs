<?php

namespace App\Exports;

use App\Models\DigitsSalesReport;
use App\Models\ReportPrivilege;
use App\Models\StoreInventoriesReport;
use App\Models\StoreInventory;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class StoreInventoryUploadBatchExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $userReport;
    private $batch;

    public function __construct($batch) {
        $this->batch = $batch;
        $this->userReport = ReportPrivilege::myReport(3,3);
    }

    public function headings(): array {
        return explode(",",$this->userReport->report_header);

    }

    public function map($item): array {

        $inventories = explode("`,`",$this->userReport->report_query);
        $inventory_report = [];

        foreach ($inventories as $key => $value) {
            array_push($inventory_report,$item->$value);
        }

        return $inventory_report;
    }

    public function query()
    {
        return StoreInventory::generateReport()
            ->where('batch_number', $this->batch)
            ->orderBy('reference_number', 'ASC');
    }
}