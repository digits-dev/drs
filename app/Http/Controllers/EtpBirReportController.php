<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EtpBirReportController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function getIndex(){

		if (request()->ajax()){
			$customers_masterfile = Cache::remember('CustomersMasterfileBIR', 3600, function() {
				return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name')->get();
			});
	
			// LOOKUP CUSTOMER
			$customerMap = [];
			foreach ($customers_masterfile as $customer) {
				$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
			}
			

			$month = request()->month;
			$year = request()->year;

			$start_date = date("Ymd", strtotime("{$year}-{$month}-01"));
    		$end_date = date("Ymd", strtotime(date("Y-m-t", strtotime($start_date))));

			$customers = array_map(function($customer) {
				return str_replace('CUS-', '', $customer);
			}, request()->customer);

			$final_bir = []; 

			foreach ($customers as $per) {

				$bir_report1 = DB::connection('sqlsrv')->select("SET NOCOUNT ON; exec [RptSpSalesTaxSummaryReport_BIR] 100, ? , $start_date , $end_date" , [$per]);

				$bir_report2 = DB::connection('sqlsrv')->select("SET NOCOUNT ON; exec [RptSpESalesSummaryReport_BIR] 100, ? , $start_date , $end_date", [$per]);
				
				$bir_report = [];

				foreach ($bir_report1 as $index => $report1) {
					if (isset($bir_report2[$index])) {
						$bir_report[] = array_merge((array)$report1, (array)$bir_report2[$index]);
					}
				}
				
				$final_bir = array_merge($final_bir, $bir_report);
			}

			foreach ($final_bir as &$row) {
				$row['CustomerName'] = $customerMap[$row['Warehouse']] ?? ' ';
				$row['CreateDate'] = Carbon::parse($row['CreateDate'])->format('Y-m-d');
			}
			

			return response()->json($final_bir);

		}

		$data = [];
		$data['page_title'] = 'BIR Report';
		$data['channels'] = Channel::active();
		$data['concepts'] = Concept::active();
		$data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
			return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
		});

		



		return view('etp-pos.etp-bir-report', $data);
	}

}
