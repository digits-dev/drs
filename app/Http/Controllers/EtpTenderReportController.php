<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtpTenderReportController extends Controller
{
	public function getIndex(Request $request)
	{
		if ($request->ajax()) {
			$param1 = $request->p1;
			$param2 = $request->p2;

			$formattedDate1 = str_replace('-', '', $param1);
			$formattedDate2 = str_replace('-', '', $param2);

			$tender_data = DB::connection('sqlsrv')->select(DB::raw("exec [SP_Custom_TenderReport] 100, '100', '0572', '$formattedDate1', '$formattedDate2'"));

			return response()->json($tender_data);
		}

		$data = [];
		$data['page_title'] = 'Tender Report';
		$data['tender_data'] = [];

		return view('etp-pos.etp-tender-report', $data);
	}
}
