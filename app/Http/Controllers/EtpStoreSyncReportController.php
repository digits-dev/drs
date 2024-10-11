<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EtpStoreSyncReportController extends Controller
{
    public function getIndex(Request $request){

		$data = [];
		$data['page_title'] = 'Store Sync';
		$data['store_sync_data'] = DB::connection('sqlsrv')->select(DB::raw("exec [SP_Custom_StoreSyncReport]"));


		return view('etp-pos.etp-storesync-report', $data);

	}
}
