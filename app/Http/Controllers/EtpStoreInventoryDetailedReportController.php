<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpStoreInventoryDetailedReportController extends \crocodicstudio\crudbooster\controllers\CBController
{

    public function getIndex(){

		if (request()->ajax()){

			$customers_masterfile = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->where(function($query) { $query->where('cutomer_name', 'like', '%FRA')->orWhere('cutomer_name', 'like', '%RTL');})->get();

			$customers = array_map(function($customer) {
				return str_replace('CUS-', '', $customer);
			}, request()->customer);

			$allCustomer = "'" . implode("','", $customers) . "'";
			$date = Carbon::parse(now())->format('Ymd');

			$inventory_report = Cache::remember("{$allCustomer}{$date}", 900, function() use($allCustomer , $date){

				return DB::connection('sqlsrv')->select(DB::raw("
				SELECT 
					P.WareHouse AS 'STORE ID',
					P.LastIssueDate AS 'DATE',
					P.ItemNumber AS 'ITEM NUMBER',
					P.LotNumber AS 'SERIAL NUMBER',
					P.BalanceApproved AS 'TOTAL QTY',
					P.Location AS 'LOCATION'
				FROM 
					ProductLocationBalance P WITH (NOLOCK)
				WHERE 
					P.Company = 100
					AND P.WareHouse IN ($allCustomer)
					AND P.LastIssueDate = $date;
				"));

			});
	
			$customerMap = [];
			$customerConcept = [];

			// LOOKUP CUSTOMER
			foreach ($customers_masterfile as $customer) {
				$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
			}

			// LOOKUP CONCEPT
			foreach ($customers_masterfile as $customer) {
				$customerConcept[str_replace('CUS-', '', $customer->customer_code)] = $customer->concept;
			}
			
			foreach ($inventory_report as $row) {
				$row->{'DATE'} = Carbon::parse($row->{'DATE'})->format('Y-m-d');
				$row->customerName = $customerMap[$row->{'STORE ID'}] ?? ' ';
				$row->concept = $customerConcept[$row->{'STORE ID'}] ?? ' ';
				$row->{'LOCATION'} = 'POS-' . $row->{'LOCATION'};

				$itemData = Cache::remember($row->{'ITEM NUMBER'}, 3600, function() use($row){

					return DB::connection('imfs')
					->table('item_masters')
					->leftJoin('brands', 'item_masters.brands_id', '=', 'brands.id')
					->where('item_masters.digits_code', $row->{'ITEM NUMBER'})
					->select('item_masters.item_description', 'brands.brand_description')
					->first();

				});

				$row->item_description = $itemData->item_description ?? ' ';
				$row->brand = $itemData->brand_description ?? ' ';

			}


			return response()->json($inventory_report);

		}

		$data = [];
		$data['page_title'] = 'Store Inventory (Detailed)';
		$data['customers'] = [];
		$data['inventory_report'] = [];
		$data['channels'] = Channel::active();
		$data['concepts'] = Concept::active();
		$data['all_customers'] = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
	
		return view('etp-pos.etp-storeinventorydetailed-report', $data);
	}
}
