<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EtpBirReportController extends Controller
{
    public function getIndex(Request $request){

		if ($request->ajax()){

			$customers = array_map(function($customer) {
				return str_replace('CUS-', '', $customer);
			}, $request->customer);
			$customers_list = implode("','", array_map('addslashes', $customers));
			$date_from = Carbon::parse($request->date_from)->format('Ymd');
			$date_to = Carbon::parse($request->date_to)->format('Ymd');

			// $store_sync_data = DB::connection('sqlsrv')->select(DB::raw("
			// 	SELECT 
			// 		Warehouse,
			// 		LEFT(MAX(EASTimeStamp), 8) AS Date,
			// 		RIGHT(MAX(EASTimeStamp), 6) AS Time
			// 	FROM 
			// 		CashOrderTrn C (NOLOCK)
			// 	WHERE 
			// 		Warehouse  IN ('$customers_list')
			// 		AND LEFT(EASTimeStamp, 8) BETWEEN '$date_from' AND '$date_to'
			// 	GROUP BY 
			// 		Warehouse
			// "));

			// return response()->json($store_sync_data);

		}

		$data = [];
		$data['page_title'] = 'BIR Report';
		$data['customers'] = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name')->where(function($query) { $query->where('cutomer_name', 'like', '%FRA')->orWhere('cutomer_name', 'like', '%RTL');})->get();

	
		return view('etp-pos.etp-bir-report', $data);
	}

}
