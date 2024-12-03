<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpESalesReport extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function getIndex(){

        if (request()->ajax()){
		
			
			return response()->json(["message" => "sample"]);

		}

        $concepts = ['BASEUS','BEYOND THE BOX','DIGITAL WALKER','OMG','OUTERSPACE','SERVICE CENTER','POP UP STORE','STORK','SOUNDPEATS','XIAOMI','CLEARANCE','OPEN SOURCE',];
        $data = [];
        $data['page_title'] = 'E-Sales Report';
		$data['page_icon'] = 'fa fa-file-text-o';
        $data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
		$data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
		$data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600 , function(){
			return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
		});

        return view('etp-pos.etp-esales-report', $data);
    }
}
