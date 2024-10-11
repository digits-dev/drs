<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtpStoreInventoryDetailedReportController extends Controller
{
    public function getIndex(Request $request){

		if ($request->ajax()){

			$customers = array_map(function($customer) {
				return str_replace('CUS-', '', $customer);
			}, $request->customer);
			$customers_list = implode("','", array_map('addslashes', $customers));
			$date_from = Carbon::parse($request->date_from)->format('Ymd');
			$date_to = Carbon::parse($request->date_to)->format('Ymd');

		}

		$data = [];
		$data['page_title'] = 'Store Inventory (Detailed)';
		$data['customers'] = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name')->where(function($query) { $query->where('cutomer_name', 'like', '%FRA')->orWhere('cutomer_name', 'like', '%RTL');})->get();
		$data['inventory_report'] = DB::connection('sqlsrv')->select(DB::raw("
			SELECT 
				P.WareHouse AS 'STORE ID',
				P.LastIssueDate AS 'DATE',
				P.ItemNumber AS 'ITEM NUMBER',
				P.LotNumber AS 'SERIAL NUMBER',
				P.BalanceApproved AS 'TOTAL QTY'
			FROM 
				ProductLocationBalance P WITH (NOLOCK)
			WHERE 
				P.Company = 100
				AND P.WareHouse = '0572'
				AND P.LastIssueDate BETWEEN '20240910' AND '20241011';
		"));
	
		return view('etp-pos.etp-storeinventorydetailed-report', $data);
	}
}
