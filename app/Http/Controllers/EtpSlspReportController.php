<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpSlspReportController extends Controller
{
    public function getIndex(){

            if (request()->ajax()){

                $month = request()->month;
			    $year = request()->year;

                $start_date = date("Ymd", strtotime("{$year}-{$month}-01"));
    		    $end_date = date("Ymd", strtotime(date("Y-m-t", strtotime($start_date))));

                $customers = array_map(function($customer) {
                    return str_replace('CUS-', '', $customer);
                }, request()->customer);

                $Customers = DB::connection('masterfile')
                    ->table('customer as cus')
                    ->select(
                        'cus.customer_code', 
                        'cus.cutomer_name', 
                        'cus.channel_id', 
                        'cus.concept', 
                        'cha.channel_description')
                    ->leftJoin(
                        'channels as cha', 
                        'cus.channel_id', '=', 'cha.id')
                    ->where(function($query) {
                        $query->where('cus.cutomer_name', 'like', '%FRA')
                            ->orWhere('cus.cutomer_name', 'like', '%RTL');
                    })->get();

                foreach ($customers as $percus){
                    $slsp_data = DB::connection('sqlsrv')->select("SET NOCOUNT ON; EXEC [RptSpSalesTaxSummaryReport_SLSP] 100, ?, $start_date, $end_date", [$percus]);
                }
                
                $customerMap = [];
                foreach ($Customers as $customer) {
                        $customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
                    }

                    foreach ($slsp_data as $row){
                        $customerCode = str_replace('CUS-', '', $row->{'Warehouse'});
                        $row->customerName = $customerMap[$customerCode] ?? 'Unknown';
                    }

                return response()->json($slsp_data); 
            }    

            $data = [];
            $data['page_title'] = 'SLSP Report';
            $data['page_icon'] = 'fa fa-file-text-o';
            $concepts = ['BASEUS','BEYOND THE BOX','DIGITAL WALKER','OMG','OUTERSPACE','SERVICE CENTER','POP UP STORE','STORK','SOUNDPEATS','XIAOMI','CLEARANCE','OPEN SOURCE',];
            $data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
            $data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
            $data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
                return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
            });

        return view('etp-pos.etp-slsp-report', $data);
    }
}
