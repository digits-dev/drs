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
use App\Models\System;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
        $report_type = ReportType::byName("STORE SALES");
        $chunk = StoreSalesUploadLine::getWithHeader($this->chunk_id);
        $rows = json_decode($chunk->chunk_data);
        foreach ($rows ?: [] as $key => $row) {
            $v_system = $system->where('system_name',$row->system)->first();
            $v_organization = $organization->where('organization_name',$row->org)->first();
            $v_channel = $channel->where('channel_code',$row->channel_code)->first();
            $v_customer = $customer->where('customer_name',$row->customer_location)->first();
            $v_employee = $employee->where('employee_name',$row->customer_location)->first();
            try {
                StoreSale::updateOrCreate([
                    'batch_number'			=> $chunk->batch,
                    'reference_number'		=> $row->reference_number,
                ],[
                    'batch_number'			=> $chunk->batch,
                    'batch_date'			=> Carbon::now()->format('Ym'),
                    'reference_number'		=> $row->reference_number,
                    'systems_id'			=> $v_system->id,
                    'organizations_id'	    => $v_organization->id,
                    'report_types_id'		=> $report_type,
                    'channels_id'	        => $v_channel->id,
                    'employees_id'          => $v_employee->id,
                    'customers_id'          => $v_customer->id,
                    'receipt_number'		=> $row->receipt_number,
                    'sales_date'			=> Carbon::parse($this->transformDate($row->sold_date))->format("Y-m-d"),
                    'item_code'				=> $row->item_number,
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
                ]);
            } catch (\Throwable $th) {
                Log::info(json_encode($th));
            }
        }
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject(intval($value)));
        } catch (\ErrorException $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }
}