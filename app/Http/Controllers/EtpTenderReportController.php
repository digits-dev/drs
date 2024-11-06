<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class EtpTenderReportController extends \crocodicstudio\crudbooster\controllers\CBController
{
	public function getIndex()
	{
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

		if (request()->ajax()) {
			$storeCustomer = request()->customer;
			$dateFrom = Carbon::parse(request()->dateFrom)->format('Ymd');
			$dateTo = Carbon::parse(request()->dateTo)->format('Ymd');
			$store = implode(',', array_map(fn($s) => "'$s'", $storeCustomer)); 

			$tender_data  = [];
			foreach ($storeCustomer as $per) {
				  $result = DB::connection('sqlsrv')->select("
				  SET NOCOUNT ON; 
				  EXEC [SP_Custom_TenderReport] 100, '100', ?, ?, ?",
				  [$per, $dateFrom, $dateTo]
			  );
			  $tender_data = array_merge($tender_data, $result);
			}

			$customerMap = [];
			foreach ($Customers as $customer) {
				$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
			}

			foreach ($tender_data as $row) {
				$ammount = $row->AMOUNT; 
				$mdr = $row->{'Commission %'};

				$mdr_charge = ($mdr * 0.01 * $ammount);
				$net_amount = ($ammount - $mdr_charge);

				$customerCode = str_replace('CUS-', '', $row->{'STORE ID'});
				$row->customerName = $customerMap[$customerCode] ?? 'Unknown';
				$row->mdrCharge = $mdr_charge ?? 'Undefined';
				$row->netAmount = $net_amount ?? 'Undefined';
				$row->{'DATE'} = Carbon::parse($row->{'DATE'})->format('Y-m-d');
				$row->{'TIME'} = Carbon::parse($row->{'TIME'})->format('H:i:s');
			}

			return response()->json($tender_data);
		}

		$concepts = ['BASEUS','BEYOND THE BOX','DIGITAL WALKER','OMG','OUTERSPACE','SERVICE CENTER','POP UP STORE','STORK','SOUNDPEATS','XIAOMI','CLEARANCE','OPEN SOURCE',];

		$data = [];
		$data['page_title'] = 'Tender Report';
		$data['page_icon'] = 'fa fa-file-text-o';
		$data['tender_data'] = [];
		$data['customers'] = $Customers;
		$data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
		$data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
		$data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
			return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
		});

		return view('etp-pos.etp-tender-report', $data);
	}
}
