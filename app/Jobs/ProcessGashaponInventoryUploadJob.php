<?php

namespace App\Jobs;

use App\Imports\GashaponInventoryImport;
use App\Models\GashaponInventoryUpload;
use App\Models\GashaponInventoryUploadLine;
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
use App\Jobs\GashaponInventoryImportJob;
class ProcessGashaponInventoryUploadJob implements ShouldQueue
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
    public $gashapon_inventory_uploads_id;

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
        $gashapon_inventory_upload = GashaponInventoryUpload::create([
            'batch' => $this->batch_number,
            'folder_name' => $this->folder_name,
            'file_name' => $this->file_name,
            'file_path' => $this->excel_path,
            'created_by' => $this->created_by,
            'from_date' => $this->from_date,
        ]);
        $this->gashapon_inventory_uploads_id = $gashapon_inventory_upload->id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        HeadingRowFormatter::default('slug');
        $excel_data = Excel::toArray(new GashaponInventoryImport($this->batch_number), $this->excel_path)[0];
  
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

        $gashapon_inventory_upload = GashaponInventoryUpload::find($this->gashapon_inventory_uploads_id);
        $gashapon_inventory_upload->update([
            'job_batches_id' => $batch->id,
            'row_count' => $row_count,
            'chunk_count' => $chunk_count,
            'headings' => json_encode($snaked_headings),
        ]);

        foreach ($chunks as $key => $chunk) {
            $json = json_encode($chunk);
            $gashapon_inventory_upload_line = new GashaponInventoryUploadLine([
                'gashapon_inventory_uploads_id' => $gashapon_inventory_upload->id,
                'chunk_index' => $key,
                'chunk_data' => $json,
            ]);

            $gashapon_inventory_upload_line->save();

            $batch->add(new GashaponInventoryImportJob($gashapon_inventory_upload_line->id));
        }

        $gashapon_inventory_upload->update(['status' => 'IMPORTING']);
        
    }

    public function failed($e) {
        $error_message = $e->getMessage();
        $gashapon_inventory_upload = GashaponInventoryUpload::find($this->gashapon_inventory_uploads_id);
        $gashapon_inventory_upload->update(['status' => 'IMPORT FAILED']);
        $gashapon_inventory_upload->appendNewError($error_message);
    }

}