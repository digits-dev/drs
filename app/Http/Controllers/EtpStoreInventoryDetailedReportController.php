<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpStoreInventoryDetailedReportController extends Controller
{
    public function getIndex(){
	
		return view('etp-pos.etp-storeinventorydetailed-report');
	}
}
