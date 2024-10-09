<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpStoreSyncReportController extends Controller
{
    public function getIndex(){
	
		return view('etp-pos.etp-storesync-report');
	}
}
