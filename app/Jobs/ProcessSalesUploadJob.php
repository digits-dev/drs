<?php

namespace App\Jobs;

use App\Imports\StoreSalesImport;
use App\Models\StoreSalesUpload;
use App\Models\StoreSalesUploadLine;
use App\Jobs\StoreSalesImportJob;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Exception;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ProcessSalesUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $batch_number;
    public $excel_path;
    public $report_type;
    public $folder_name;
    public $file_name;
    public $created_by;
    public $timeout = 3600;
    public $store_sales_upload_id;

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
        $store_sales_upload = StoreSalesUpload::create([
            'batch' => $this->batch_number,
            'folder_name' => $this->folder_name,
            'file_name' => $this->file_name,
            'file_path' => $this->excel_path,
            'created_by' => $this->created_by,
        ]);
        $this->store_sales_upload_id = $store_sales_upload->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            HeadingRowFormatter::default('slug');
            $excel_data = Excel::toArray(new StoreSalesImport($this->batch_number), $this->excel_path)[0];
            $errors = [];
    
            $excelReportType = array_unique(array_column($excel_data, "report_type"));
            foreach ($excelReportType as $keyReportType => $valueReportType) {
                if (!in_array($valueReportType, $this->report_type)) {
                    array_push($errors, 'report type "'.$valueReportType.'" mismatched!');
                }
            }
    
            if (!empty($errors)) {
                // TODO: insert to db for error uploads
                return;
            }
    
            $snaked_headings = array_keys($excel_data[0]);
            $row_count = count($excel_data);
            $chunk_count = 500;
            $chunks = array_chunk($excel_data, $chunk_count);
            $batch = Bus::batch([])->dispatch();

            $store_sales_upload = StoreSalesUpload::find($this->store_sales_upload_id);
            $store_sales_upload->update([
                'job_batches_id' => $batch->id,
                'row_count' => $row_count,
                'chunk_count' => $chunk_count,
                'headings' => json_encode($snaked_headings),
            ]);
            
            foreach ($chunks as $key => $chunk) {
                $json = json_encode($chunk);
                $store_sales_upload_line = new StoreSalesUploadLine([
                    'store_sales_uploads_id' => $store_sales_upload->id,
                    'chunk_index' => $key,
                    'chunk_data' => $json,
                ]);
    
                $store_sales_upload_line->save();
    
                $batch->add(new StoreSalesImportJob($store_sales_upload_line->id));
            }

        } catch (Exception $e) {
            Log::error('Error processing the job: ' . $e->getMessage());
        }

    }
}