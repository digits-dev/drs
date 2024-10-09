<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpBirReportController extends Controller
{
    public function getIndex(){
	
		return view('etp-pos.etp-bir-report');
	}
}
