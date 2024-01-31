<?php

namespace App\Jobs;

use App\Imports\WarehouseInventoryImport;
use App\Models\WarehouseInventoryUpload;
use App\Models\WarehouseInventoryUploadLine;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ProcessWarehouseInventoryUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $batch_number;
    public $excel_path;
    public $report_type;
    public $folder_name;
    public $file_name;
    public $created_by;
    public $timeout = 3600;
    public $from_date;
    public $warehouse_inventory_uploads_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($args)
    {
        $this->args = $args;
        $this->batch_number = $args['batch_number'];
        $this->excel_path = $args['excel_path'];
        $this->report_type = $args['report_type'];
        $this->folder_name = $args['folder_name'];
        $this->file_name = $args['file_name'];
        $this->file_path = $args['file_path'];
        $this->created_by = $args['created_by'];
        $this->from_date = $args['from_date'];
        $warehouse_inventory_uploads = WarehouseInventoryUpload::create([
            'batch' => $this->batch_number,
            'folder_name' => $this->folder_name,
            'file_name' => $this->file_name,
            'file_path' => $this->excel_path,
            'created_by' => $this->created_by,
            'from_date' => $this->from_date,
        ]);
        $this->warehouse_inventory_uploads_id = $warehouse_inventory_uploads->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        HeadingRowFormatter::default('slug');
        $excel_data = Excel::toArray(new WarehouseInventoryImport($this->batch_number), $this->excel_path)[0];

        $excelReportType = array_unique(array_column($excel_data, "report_type"));
        foreach ($excelReportType as $keyReportType => $valueReportType) {
            if (!in_array($valueReportType, $this->report_type)) {
                throw new Exception("INVALID REPORT TYPE: $valueReportType");
            }
        }

        $snaked_headings = array_keys($excel_data[0]);
        $row_count = count($excel_data);
        $chunk_count = 1000;
        $chunks = array_chunk($excel_data, $chunk_count);
        $batch = Bus::batch([])->dispatch();

        $warehouse_inventory_upload = WarehouseInventoryUpload::find($this->warehouse_inventory_uploads_id);
        $warehouse_inventory_upload->update([
            'job_batches_id' => $batch->id,
            'row_count' => $row_count,
            'chunk_count' => $chunk_count,
            'headings' => json_encode($snaked_headings),
        ]);

        foreach ($chunks as $key => $chunk) {
            $json = json_encode($chunk);
            $warehouse_inventory_upload_line = new WarehouseInventoryUploadLine([
                'warehouse_inventory_uploads_id' => $warehouse_inventory_upload->id,
                'chunk_index' => $key,
                'chunk_data' => $json,
            ]);

            $warehouse_inventory_upload_line->save();
            $batch->add(new WarehouseInventoryImportJob($warehouse_inventory_upload_line->id));
        }

        $warehouse_inventory_upload->update(['status' => 'IMPORTING']);
        
    }

    public function failed($e) {
        $error_message = $e->getMessage();
        $warehouse_inventory_upload = WarehouseInventoryUpload::find($this->warehouse_inventory_uploads_id);
        $warehouse_inventory_upload->update(['status' => 'IMPORT FAILED']);
        $warehouse_inventory_upload->appendNewError($error_message);
    }
}
