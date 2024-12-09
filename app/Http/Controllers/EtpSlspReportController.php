<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EtpSlspReportController extends Controller
{
    public function getIndex()
    {

        if (request()->ajax()) {

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

            $slsp_data = [];

            foreach ($customers as $percus) {
                $result = DB::connection('sqlsrv')->select("SET NOCOUNT ON; EXEC [RptSpESalesSummaryReport_SLSP] 100, 100, ?, $start_date, $end_date", [$percus]);
                $slsp_data = array_merge($slsp_data, $result);
            }

            $customerMap = [];
            foreach ($Customers_data as $customer) {
                $customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
            }

            foreach ($slsp_data as $row) {
                $customerCode = str_replace('CUS-', '', $row->{'STORE_ID'});
                $row->customerName = $customerMap[$customerCode] ?? 'Unknown';
                $row->TAXABLE_MONTH = Carbon::parse($row->TAXABLE_MONTH)->format('m/d/Y');
                
                $taxable_sales = $row->VAT_TOTAL_SALES - $row->VAT_TOTAL_AMT;
                $gross_sales = $row->EXEMPT_SALES + $row->ZERO_RATED_SALES + $taxable_sales;  
                $output_tax = $taxable_sales * 0.12;
                $gross_taxable_sales = $gross_sales + $output_tax;

                $formatted_gross_sales = floor($gross_sales * 100) / 100;
                $exempt_sales = floor($row->EXEMPT_SALES * 100) / 100;
                $zero_rated_sales = floor($row->ZERO_RATED_SALES * 100) / 100;
                $formatted_taxable_sales = floor($taxable_sales * 100) / 100;
                $formatted_output_tax = floor($output_tax * 100) / 100;
                $formatted_gross_taxable_sales = floor($gross_taxable_sales * 100) / 100;

                $final_gross_sales = number_format($formatted_gross_sales, 2, '.', '');
                $final_exempt_sales = number_format($exempt_sales, 2, '.', '');
                $final_zero_rated_sales = number_format($zero_rated_sales, 2, '.', '');
                $final_taxable_sales = number_format($formatted_taxable_sales, 2, '.', '');
                $final_output_tax = number_format($formatted_output_tax, 2, '.', '');
                $final_gross_taxable_sales = number_format($formatted_gross_taxable_sales, 2, '.', '');

                $row->GROSS_SALES = abs($final_gross_sales);
                $row->EXEMPT_SALES = abs($final_exempt_sales);
                $row->ZERO_RATED_SALES = abs($final_zero_rated_sales);
                $row->TAXABLE_SALES = abs($final_taxable_sales);
                $row->OUTPUT_TAX = abs($final_output_tax);
                $row->GROSS_TAXABLE_SALES = abs($final_gross_taxable_sales);
            }

            return response()->json($slsp_data);
        }

        $data = [];
        $data['page_title'] = 'SLSP Report';
        $data['page_icon'] = 'fa fa-file-text-o';
        $concepts = ['BASEUS', 'BEYOND THE BOX', 'DIGITAL WALKER', 'OMG', 'OUTERSPACE', 'SERVICE CENTER', 'POP UP STORE', 'STORK', 'SOUNDPEATS', 'XIAOMI', 'CLEARANCE', 'OPEN SOURCE',];
        $data['channels'] = Channel::whereIn('channel_name', ['RETAIL', 'FRANCHISE'])->active();
        $data['concepts'] = Concept::whereIn('concept_name', $concepts)->active();
        $data['all_customers'] = Cache::remember('CustomerMasterfileCache', 3600, function () {
            return DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();
        });

        return view('etp-pos.etp-slsp-report', $data);
    }
}
