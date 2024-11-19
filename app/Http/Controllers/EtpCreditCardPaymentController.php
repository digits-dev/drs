<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EtpCreditCardPaymentController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function getIndex(){
		$data = [];
		$data['page_title'] = 'Credit Card Payment';
		$data['page_icon'] = 'fa fa-file-text-o';
		$data['credit_card_payment'] = DB::connection('sqlsrv')
										->select(DB::raw("
										SELECT AgencyId as 'PaymentCode', 
										Name as 'CreditCardPaymentName', 
										Description as 'CreditCardPaymentDescription', 
										CASE 
											WHEN Active = 2 THEN 'ACTIVE'
											ELSE 'INACTIVE'
										END as 'Status', 
										CreateDate 
										FROM agency 
										WHERE Company=100 
										and Warehouse=00000
										"));
		// dd($data['credit_card_payment']);

		return view('etp-pos.etp-credit-card-payment', $data);
	}

}
