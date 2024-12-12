<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpVatReport extends Controller
{
    public function getIndex(){

        $Customers = Cache::remember('filtered_customers', 900, function() {
            return DB::connection('masterfile')
                ->table('customer as cus')
                ->select('cus.customer_code', 'cus.cutomer_name', 'cus.channel_id', 'cus.concept', 'cha.channel_description')
                ->leftJoin('channels as cha', 'cus.channel_id', '=', 'cha.id')
                ->where(function($query) {
                    $query->where('cus.cutomer_name', 'like', '%FRA')
                        ->orWhere('cus.cutomer_name', 'like', '%RTL');
                })->get();
        });

        if (request()->ajax()){
           
			$dateFrom = Carbon::parse(request()->dateFrom)->format('Ymd');
			$dateTo = Carbon::parse(request()->dateTo)->format('Ymd');

            $customers = array_map(function ($customer) {
                return str_replace('CUS-', '', $customer);
            }, request()->customer);

            $Customers_data = DB::connection('masterfile')
                ->table('customer as cus')
                ->select(
                    'cus.customer_code',
                    'cus.cutomer_name',
                    'cus.channel_id',
                    'cus.concept',
                    'cha.channel_description'
                )
                ->leftJoin(
                    'channels as cha',
                    'cus.channel_id',
                    '=',
                    'cha.id'
                )
                ->where(function ($query) {
                    $query->where('cus.cutomer_name', 'like', '%FRA')
                        ->orWhere('cus.cutomer_name', 'like', '%RTL');
                })->get();

            $vat_data = [];

            foreach ($customers as $percus) {
                $result = DB::connection('sqlsrv')->select("SET NOCOUNT ON; EXEC [RptSpESalesSummaryReport_VATREPORT_SBJ] 100, 100, ?, $dateFrom, $dateTo", [$percus]);
                $vat_data = array_merge($vat_data, $result);
            }
    
            $customerMap = [];
            foreach ($Customers_data as $customer) {
                $customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
            }

            foreach ($vat_data as $row){
                $row->DATE = Carbon::parse($row->DATE)->format('Y-m-d');
            }

            return response()->json($vat_data);

        }

		$concepts = ['BASEUS','BEYOND THE BOX','DIGITAL WALKER','OMG','OUTERSPACE','SERVICE CENTER','POP UP STORE','STORK','SOUNDPEATS','XIAOMI','CLEARANCE','OPEN SOURCE',];

        $data = [];
        $data['page_title'] = 'VAT Report';
		$data['page_icon'] = 'fa fa-file-text-o';
        $data['tender_data'] = [];
		$data['customers'] = $Customers;
		$data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
		$data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
		$data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
			return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
		});

        return view('etp-pos.etp-vat-report', $data);
    }
}
