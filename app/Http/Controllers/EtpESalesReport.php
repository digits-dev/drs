<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpESalesReport extends Controller
{
    public function getIndex(){

        $data = [];
        $data['page_title'] = 'E-Sales Report';
		$data['page_icon'] = 'fa fa-file-text-o';

        return view('etp-pos.etp-esales-report', $data);
    }
}
