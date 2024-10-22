<?php

namespace App\Jobs;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\System;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\StoreSale;
use App\Models\ReportType;
use App\Models\AppleCutoff;
use App\Models\Organization;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\NonAppleCutoff;
use App\Models\StoreInventory;
use App\Models\StoreSalesUpload;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Log;
use App\Models\StoreInventoryUpload;
use App\Models\StoreSalesUploadLine;
use Illuminate\Queue\SerializesModels;
use App\Models\InventoryTransactionType;
use App\Models\StoreInventoryUploadLine;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class StoreInventoryImportJobPull implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chunk;
    private $system;
    private $organization;
    private $channel;
    private $sub_inventory;
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
        $sub_inventory = InventoryTransactionType::active();
        $employee = Employee::active();
        $report_type = ReportType::active();
        $chunk = StoreInventoryUploadLine::getWithHeader($this->chunk_id);
        $rows = json_decode($chunk->chunk_data);
        $insertable = [];


        foreach ($rows ?: [] as $key => $row) {

            $v_system = $system->where('system_name',$row->system)->first();
            $v_organization = $organization->where('organization_name',$row->org)->first();
            $v_report_type = $report_type->where('report_type',$row->report_type)->first();
            $v_channel = $channel->where('channel_code',trim($row->channel_code))->first();
            $v_sub_inventory = $sub_inventory->where('inventory_transaction_type', trim($row->sub_inventory))->first();
            $v_customer = $customer->where('customer_name',trim($row->customer_location))->first();
            $v_employee = $employee->where('employee_name',trim($row->customer_location))->first();
            $inventory_as_of_date = $row->inventory_as_of_date;
  

            $insertable[] = [
                'batch_number'			=> $chunk->batch,
                'batch_date'			=> Carbon::now()->format('Ym'),
                'reference_number'		=> $row->reference_number,
                'systems_id'			=> $v_system->id,
                'organizations_id'	    => $v_organization->id,
                'report_types_id'		=> $v_report_type->id,
                'channels_id'	        => $v_channel->id,
                'inventory_transaction_types_id' =>$v_sub_inventory->id,
                'employees_id'          => $v_employee->id,
                'customers_id'          => $v_customer->id,
                'inventory_date'		=> $inventory_as_of_date,
                'item_code'				=> trim($row->item_number), 
                'item_description'      => trim(preg_replace('/\s+/', ' ', strtoupper($row->item_description))),
                'quantity_inv'			=> $row->inventory_qty,
                'store_cost'			=> $row->store_cost,
                'dtp_ecom'			    => $row->store_cost_ecomm,
                'qtyinv_sc'			    => ($row->inventory_qty)*($row->store_cost),
                'qtyinv_ecom'			=> ($row->inventory_qty)*($row->store_cost_ecomm),
                'landed_cost'			=> $row->landed_cost,
                'product_quality'	    => $row->product_quality,
                'to_warehouse'          => $row->to_warehouse,
                'from_warehouse'        => $row->from_warehouse,
                'qtyinv_lc'			    => ($row->inventory_qty)*($row->landed_cost),
                'created_by'            => $chunk->created_by,
            ];
        }
        $columns = array_keys($insertable[0]);
        StoreInventory::upsert($insertable, ['batch_number', 'reference_number'], $columns);

        $count = StoreInventory::where('batch_number', $chunk->batch)->count();
        Log::info("$count of $chunk->row_count rows imported for batch #$chunk->batch.");
        if ($count == $chunk->row_count) {
            $store_sales_upload = StoreInventoryUpload::find($chunk->store_inventory_uploads_id);
            $store_sales_upload->update(['status' => 'IMPORT FINISHED']);
        }
    }
}
