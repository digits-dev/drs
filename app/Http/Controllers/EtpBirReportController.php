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

				// Truncate to 2 decimal points without rounding
				$netAmount = floor($row['NetAmount'] * 100) / 100;
				$discount = floor($row['Discount'] * 100) / 100;
				$returns = floor($row['Returns'] * 100) / 100;
				$voids = floor($row['Voids'] * 100) / 100;
				$deductions = floor($row['Deductions'] * 100) / 100;
				$grossTotalAmt = floor($row['GrossTotalAmt'] * 100) / 100;
				$vatableTotalAmt = floor($row['VatableTotalAmt'] * 100) / 100;
				$vatTotalAmt = floor($row['VatTotalAmt'] * 100) / 100;
				$salesVatExmptAmt = floor($row['SalesVatExmptAmt'] * 100) / 100;
				$zeroRatedSalesAmt = floor($row['ZeroRatedSalesAmt'] * 100) / 100;

				// Format the values to 2 decimal points only
				$netAmount = number_format($netAmount, 2, '.', '');
				$discount = number_format($discount, 2, '.', '');
				$returns = number_format($returns, 2, '.', '');
				$voids = number_format($voids, 2, '.', '');
				$deductions = number_format($deductions, 2, '.', '');
				$grossTotalAmt = number_format($grossTotalAmt, 2, '.', '');
				$vatableTotalAmt = number_format($vatableTotalAmt, 2, '.', '');
				$vatTotalAmt = number_format($vatTotalAmt, 2, '.', '');
				$salesVatExmptAmt = number_format($salesVatExmptAmt, 2, '.', '');
				$zeroRatedSalesAmt = number_format($zeroRatedSalesAmt, 2, '.', '');

				$row['NetAmount'] = $netAmount;
				$row['Discount'] = $discount;
				$row['Returns'] = $returns;
				$row['Voids'] = $voids;
				$row['Deductions'] = $deductions;
				$row['GrossTotalAmt'] = $grossTotalAmt;
				$row['VatableTotalAmt'] = $vatableTotalAmt;
				$row['VatTotalAmt'] = $vatTotalAmt;
				$row['SalesVatExmptAmt'] = $salesVatExmptAmt;
				$row['ZeroRatedSalesAmt'] = $zeroRatedSalesAmt;
			}
			
			return response()->json($final_bir);

		}

		$concepts = ['BASEUS','BEYOND THE BOX','DIGITAL WALKER','OMG','OUTERSPACE','SERVICE CENTER','POP UP STORE','STORK','SOUNDPEATS','XIAOMI','CLEARANCE','OPEN SOURCE',];

		$data = [];
		$data['page_title'] = 'BIR Report';
		$data['page_icon'] = 'fa fa-file-text-o';
		$data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
		$data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
		$data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
			return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
		});


		return view('etp-pos.etp-bir-report', $data);
	}

}
