<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EtpVatReport extends Controller
{
    public function getIndex(){

        $data = [];
        $data['page_title'] = 'VAT Report';
		$data['page_icon'] = 'fa fa-file-text-o';

        return view('etp-pos.etp-vat-report', $data);
    }
}
