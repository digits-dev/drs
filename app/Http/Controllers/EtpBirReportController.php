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

			$customers = array_map(function($customer) {
				return str_replace('CUS-', '', $customer);
			}, request()->customer);
			$customers_list = implode("','", array_map('addslashes', $customers));
			$date_from = Carbon::parse(request()->date_from)->format('Ymd');
			$date_to = Carbon::parse(request()->date_to)->format('Ymd');

			$store_sync_data = DB::connection('sqlsrv')->select(DB::raw("
				SELECT 
					Warehouse,
					LEFT(MAX(EASTimeStamp), 8) AS Date,
					RIGHT(MAX(EASTimeStamp), 6) AS Time
				FROM 
					CashOrderTrn C (NOLOCK)
				WHERE 
					Warehouse  IN ('$customers_list')
					AND LEFT(EASTimeStamp, 8) BETWEEN '$date_from' AND '$date_to'
				GROUP BY 
					Warehouse
			"));

			return response()->json($store_sync_data);

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
