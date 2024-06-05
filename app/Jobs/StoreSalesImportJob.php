<?php

namespace App\Jobs;

use App\Models\StoreSalesUploadLine;
use App\Models\AppleCutoff;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\NonAppleCutoff;
use App\Models\Organization;
use App\Models\ReportType;
use App\Models\StoreSale;
use App\Models\StoreSalesUpload;
use App\Models\System;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class StoreSalesImportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chunk;
    private $system;
    private $organization;
    private $channel;
    private $customer;
    private $employee;
    private $batch_number;
    private $report_type;
    private $apple_cutoff;
    private $non_apple_cutoff;
    private $chunk_id;
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chunk_id)
    {
        $this->chunk_id = $chunk_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $system = System::active();
        $organization = Organization::active();
        $channel = Channel::active();
        $customer = Customer::active();
        $employee = Employee::active();
        $report_type = ReportType::active();
        $chunk = StoreSalesUploadLine::getWithHeader($this->chunk_id);
        $rows = json_decode($chunk->chunk_data);
        $insertable = [];
        foreach ($rows ?: [] as $key => $row) {
            $v_system = $system->where('system_name',$row->system)->first();
            $v_organization = $organization->where('organization_name',$row->org)->first();
            $v_report_type = $report_type->where('report_type',$row->report_type)->first();
            $v_channel = $channel->where('channel_code',$row->channel_code)->first();
            $v_customer = $customer->where('customer_name',$row->customer_location)->first();
            $v_employee = $employee->where('employee_name',$row->customer_location)->first();
            $sales_date = date('Y-m-d', strtotime('1899-12-30') + ($row->sold_date * 24 * 60 * 60));
            if ($sales_date < $chunk->from_date || $sales_date > $chunk->to_date) {
                throw new Exception("SALES DATE OUT OF RANGE FOR REF #$row->reference_number.");
            }
            $insertable[] = [
                'batch_number'			=> $chunk->batch,
                'batch_date'			=> Carbon::now()->format('Ym'),
                'reference_number'		=> $row->reference_number,
                'systems_id'			=> $v_system->id,
                'organizations_id'	    => $v_organization->id,
                'report_types_id'		=> $v_report_type->id,
                'channels_id'	        => $v_channel->id,
                'employees_id'          => $v_employee->id,
                'customers_id'          => $v_customer->id,
                'receipt_number'		=> $row->receipt_number,
                // 'sales_date'			=> DateTime::createFromFormat('m/d/Y', $row->sold_date)->format('Y-m-d'),
                'sales_date'			=> $sales_date,
                'item_code'				=> trim($row->item_number),
                'digits_code_rr_ref'    => $row->rr_ref,
                'item_description'      => trim(preg_replace('/\s+/', ' ', strtoupper($row->item_description))),
                'quantity_sold'			=> $row->qty_sold,
                'sold_price'			=> $row->sold_price,
                'qtysold_price'			=> ($row->qty_sold)*($row->sold_price),
                'net_sales'				=> $row->net_sales,
                'store_cost'			=> $row->store_cost,
                'dtp_ecom'			    => $row->store_cost_ecomm,
                'qtysold_sc'			=> ($row->qty_sold)*($row->store_cost),
                'qtysold_ecom'			=> ($row->qty_sold)*($row->store_cost_ecomm),
                'landed_cost'			=> $row->landed_cost,
                'qtysold_lc'			=> ($row->qty_sold)*($row->landed_cost),
                'sale_memo_reference'	=> $row->sale_memo_ref,
                'created_by'            => $chunk->created_by,
            ];
        }
        $columns = array_keys($insertable[0]);
        StoreSale::upsert($insertable, ['batch_number', 'reference_number'], $columns);

        $count = StoreSale::where('batch_number', $chunk->batch)->count();
        Log::info("$count of $chunk->row_count rows imported for batch #$chunk->batch.");
        if ($count == $chunk->row_count) {
            $store_sales_upload = StoreSalesUpload::find($chunk->store_sales_uploads_id);
            $store_sales_upload->update(['status' => 'IMPORT FINISHED']);
        }
    }

    public function failed($e) {
        $chunk = StoreSalesUploadLine::getWithHeader($this->chunk_id);
        $error_message = $e->getMessage();
        $store_sales_upload = StoreSalesUpload::find($chunk->store_sales_uploads_id);
        $store_sales_upload->update(['status' => 'IMPORT FAILED']);
        $store_sales_upload->appendNewError($error_message);
    }
}
