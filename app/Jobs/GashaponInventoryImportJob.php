<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryTransactionType;
use App\Models\Organization;
use App\Models\ReportType;
use App\Models\GashaponInventory;
use App\Models\GashaponInventoryUpload;
use App\Models\GashaponInventoryUploadLine;
use App\Models\System;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GashaponInventoryImportJob implements ShouldQueue
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
    private $inventory_transaction_type;
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
        $inventory_transaction_type = InventoryTransactionType::active();
        $chunk = GashaponInventoryUploadLine::getWithHeader($this->chunk_id);
        $rows = json_decode($chunk->chunk_data);
        $insertable = [];

        foreach ($rows ?: [] as $key => $row) {
            $v_system = $system->where('system_name',$row->system)->first();
            $v_organization = $organization->where('organization_name',$row->org)->first();
            $v_report_type = $report_type->where('report_type',$row->report_type)->first();
            $v_inventory_transaction_type = $inventory_transaction_type->where('inventory_transaction_type',trim($row->sub_inventory))->first();
            $v_channel = $channel->where('channel_code',trim($row->channel_code))->first();
            $v_customer = $customer->where('customer_name',trim($row->customer_location))->first();
            $v_employee = $employee->where('employee_name',trim($row->customer_location))->first();
            $inventory_date = date('Y-m-d', strtotime('1899-12-30') + ($row->inventory_as_of_date * 24 * 60 * 60));
            if ($inventory_date != $chunk->from_date) {
                throw new Exception("INVENTORY DATE MISMATCHED FOR REF #$row->reference_number ($inventory_date)");
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
                'inventory_transaction_types_id' => $v_inventory_transaction_type->id,
                'inventory_date'		=> $inventory_date,
                'item_code'				=> trim($row->item_number),
                'item_description'      => trim(preg_replace('/\s+/', ' ', strtoupper($row->item_description))),
                'quantity_inv'			=> $row->inventory_qty,
                'store_cost'			=> $row->store_cost,
                'dtp_ecom'			    => $row->store_cost_ecomm,
                'qtyinv_sc'			    => ($row->inventory_qty)*($row->store_cost),
                'qtyinv_ecom'			=> ($row->inventory_qty)*($row->store_cost_ecomm),
                'landed_cost'			=> $row->landed_cost,
                'product_quality'	    => $row->product_quality,
                'qtyinv_lc'			    => ($row->inventory_qty)*($row->landed_cost),
                'created_by'            => $chunk->created_by,
            ];
        }

        $columns = array_keys($insertable[0]);
        GashaponInventory::upsert($insertable, ['batch_number', 'reference_number'], $columns);

        $count = GashaponInventory::where('batch_number',$chunk->batch)->count();

        if ($count == $chunk->row_count) {
            $gashapon_inventory_upload = GashaponInventoryUpload::find($chunk->gashapon_inventory_uploads_id);
            $gashapon_inventory_upload->update(['status' => 'IMPORT FINISHED']);
        }
    }

    public function failed($e) {
        $error_message = $e->getMessage();
        $chunk = GashaponInventoryUploadLines::getWithHeader($this->chunk_id);
        $gashapon_inventory_upload = GashaponInventoryUpload::find($chunk->gashapon_inventory_uploads_id);
        $gashapon_inventory_upload->update(['status' => 'IMPORT FAILED']);
        $gashapon_inventory_upload->appendNewError($error_message);
    }
}