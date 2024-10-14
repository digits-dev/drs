<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EtpStoreSyncReportController extends \crocodicstudio\crudbooster\controllers\CBController {


    public function getIndex(){
		
		$customerMap = [];
		$customers_masterfile = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->where(function($query) { $query->where('cutomer_name', 'like', '%FRA')->orWhere('cutomer_name', 'like', '%RTL');})->get();
		foreach ($customers_masterfile as $customer) {
			$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
		}

		$data = [];
		$data['page_title'] = 'Store Sync';
		$data['store_sync_data'] = DB::connection('sqlsrv')->select(DB::raw("exec [SP_Custom_StoreSyncReport]"));

		foreach ($data['store_sync_data'] as $row) {
			$row->{'Warehouse'} = $customerMap[$row->{'Warehouse'}] ?? ' ';
			$row->{'Date'} = Carbon::parse($row->{'Date'})->format('Y-m-d');
			$row->{'Time'} = Carbon::createFromFormat('His', $row->{'Time'})->format('h:i:s A');

		}

		return view('etp-pos.etp-storesync-report', $data);

	}
}
