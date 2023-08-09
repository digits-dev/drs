<?php

namespace App\Imports;

use App\Models\Channel;
use App\Models\Customer;
use App\Models\DigitsSale;
use App\Models\Organization;
use App\Models\System;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use CRUDBooster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DigitsSalesImport implements ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithValidation,
    WithEvents,
    ShouldQueue
{

    use Importable, RegistersEventListeners, Queueable, InteractsWithQueue, SkipsFailures;

    private $system;
    private $organization;
    private $channel;
    private $customer;
    private $batch_number;
    private $report_type;

    public function __construct($batch_number,$report_type)
    {
        $this->system = System::active();
        $this->organization = Organization::active();
        $this->channel = Channel::active();
        $this->customer = Customer::active();
        $this->batch_number = $batch_number;
        $this->report_type = $report_type ?? 1;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $v_system = $this->system->where('system_name',$row['system'])->first();
        $v_organization = $this->organization->where('organization_name',$row['org'])->first();
        $v_channel = $this->channel->where('channel_code',$row['channel_code'])->first();
        $v_customer = $this->customer->where('customer_name',$row['customer_location'])->first();

        DigitsSale::updateOrCreate([
            'batch_number'			=> $this->batch_number,
            'reference_number'		=> $row['reference_number'],
        ],[
            'batch_date'			=> Carbon::now()->format('Ym'),
            'reference_number'		=> $row['reference_number'],
            'systems_id'			=> $v_system->id ?? NULL,
            'organizations_id'	    => $v_organization->id ?? NULL,
            'report_types_id'		=> $this->report_type,
            'channels_id'	        => $v_channel->id ?? NULL,
            'customers_id'          =>  $v_customer->id ?? NULL,
            // 'customer_location'		=> $row['customer_location'],
            'receipt_number'		=> $row['receipt_number'],
            'sales_date'			=> Carbon::parse($row["sold_date"])->format("Y-m-d"),
            'item_code'				=> $row['item_number'],
            'digits_code_rr_ref'    => $row['rr_ref'],
            'item_description'      => trim(preg_replace('/\s+/', ' ', strtoupper($row['item_description']))),
            'quantity_sold'			=> $row['qty_sold'],
            'sold_price'			=> $row['sold_price'],
            'qtysold_price'			=> ($row['qty_sold'])*($row['sold_price']),
            'net_sales'				=> $row['net_sales'],
            'store_cost'			=> $row['store_cost'],
            'dtp_ecom'			    => $row['store_cost_ecomm'],
            'qtysold_sc'			=> ($row['qty_sold'])*($row['store_cost']),
            'qtysold_ecom'			=> ($row['qty_sold'])*($row['store_cost_ecomm']),
            'landed_cost'			=> $row['landed_cost'],
            'qtysold_lc'			=> ($row['qty_sold'])*($row['landed_cost']),
            'sale_memo_reference'	=> $row['sale_memo_ref'],
            'batch_number'			=> $this->batch_number,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
            '*.system' => ['required'],
            '*.org' => ['required'],
            '*.channel_code' => ['required'],
            '*.customer_location' => ['required'],
            '*.receipt_number' => ['required']
        ];
    }

    public function onError(\Throwable $e)
    {
        \Log::error(json_encode($e));
    }

    public static function afterImport(AfterImport $event)
    {
        $config['content'] = "Your import job is complete!";
        $config['to'] = CRUDBooster::adminPath('financial_reports');
        $config['id_cms_users'] = [CRUDBooster::myid()];
        CRUDBooster::sendNotification($config);
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }
}
