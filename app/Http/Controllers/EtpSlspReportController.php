<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpSlspReportController extends Controller
{
    public function getIndex(){

        $data = [];
        $data['page_title'] = 'SLSP Report';
		$data['page_icon'] = 'fa fa-file-text-o';

        return view('etp-pos.etp-slsp-report', $data);
    }
}
