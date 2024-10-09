<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpTenderReportController extends Controller
{
    public function getIndex(){
	
		return view('etp-pos.etp-tender-report');
	}
}
