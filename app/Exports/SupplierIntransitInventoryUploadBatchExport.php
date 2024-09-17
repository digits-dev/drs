<?php

namespace App\Exports;

use App\Models\DigitsSalesReport;
use App\Models\ReportPrivilege;
use App\Models\SupplierIntransitInventoriesReport;
use App\Models\SupplierIntransitInventory;
use App\Models\SupplierIntransitInventoryUpload;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SupplierIntransitInventoryUploadBatchExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithCustomChunkSize, WithStrictNullComparison
{
    use Exportable;
    private $userReport;
    private $batch;

    public function __construct($batch) {
        $this->batch = $batch;
        SupplierIntransitInventoryUpload::where('batch', $batch)->update(['status' => 'GENERATING FILE']);
        $this->userReport = ReportPrivilege::myReport(9,CRUDBooster::myPrivilegeId());
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
        return SupplierIntransitInventory::generateReport()
            ->where('batch_number', $this->batch)
            ->orderBy('reference_number', 'ASC');
    }

    public function failed($exception) : void {
        $error_message = $exception->getMessage();
        $warehouse_inventory_upload = SupplierIntransitInventoryUpload::where('batch', $this->batch)->first();
        $warehouse_inventory_upload->status = 'FAILED TO GENERATE FILE';
        $warehouse_inventory_upload->save();
        $warehouse_inventory_upload->appendNewError($error_message);
    }

    public function chunkSize(): int {
        return 1000;
    }
}