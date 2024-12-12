<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpESalesReport extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function getIndex(){

        if (request()->ajax()){
		
			$month = request()->month;
            $year = request()->year;

            $start_date = date("Ymd", strtotime("{$year}-{$month}-01"));
            $end_date = date("Ymd", strtotime(date("Y-m-t", strtotime($start_date))));

            $customers = array_map(function ($customer) {
                return str_replace('CUS-', '', $customer);
            }, request()->customer);

            $Customers_data = DB::connection('masterfile')
                ->table('customer as cus')
                ->select(
                    'cus.customer_code',
                    'cus.cutomer_name',
                    'cus.channel_id',
                    'cus.concept',
                    'cha.channel_description'
                )
                ->leftJoin(
                    'channels as cha',
                    'cus.channel_id',
                    '=',
                    'cha.id'
                )
                ->where(function ($query) {
                    $query->where('cus.cutomer_name', 'like', '%FRA')
                        ->orWhere('cus.cutomer_name', 'like', '%RTL');
                })->get();

            $eSales_data = [];

            foreach ($customers as $percus) {
                $result = DB::connection('sqlsrv')->select("SET NOCOUNT ON; EXEC [RptSpESalesSummaryReport_ESALES] 100, 100, ?, $start_date, $end_date", [$percus]);
                $eSales_data = array_merge($eSales_data, $result);
            }

            $customerMap = [];
            foreach ($Customers_data as $customer) {
                $customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
            }

            foreach ($eSales_data as $row) {
                $month = Carbon::parse($row->CREATEDDATE)->translatedFormat('F');
                $year = Carbon::parse($row->CREATEDDATE)->format('Y');
                $vatable_sales = $row->VAT_TOTAL_SALES - $row->VAT_TOTAL_AMT;
                
                $TIN_no = $row->{'TIN_#'};
                if (!empty($TIN_no)) {
                    if (preg_match('/-(\d+)$/', $TIN_no, $matches)) {
                        $branch = $matches[1]; 
                    } else {
                        $branch = '';
                    }
                } else {
                    $branch = '';
                }

                $formatted_vatable_sales = floor($vatable_sales * 100) / 100;
                $formatted_zero_rated_sales = floor($row->ZERO_RATED_SALES * 100) / 100;
                $formatted_exempt_sales = floor($row->EXEMPT_SALES * 100) / 100;

                $final_vatable_sales = number_format($formatted_vatable_sales, 2, '.', '');
                $final_zero_rated_sales = number_format($formatted_zero_rated_sales, 2, '.', '');
                $final_exempt_sales = number_format($formatted_exempt_sales, 2, '.', '');
                
                $row->Formatted_BRANCH = $branch;
                $row->MONTH = $month;
                $row->YEAR = $year;
                $row->vatable_sales = abs($final_vatable_sales);
                $row->ZERO_RATED_SALES = abs($final_zero_rated_sales);
                $row->EXEMPT_SALES = abs($final_exempt_sales);
            }

            return response()->json($eSales_data);

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
