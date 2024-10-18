<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class EtpTenderReportController extends \crocodicstudio\crudbooster\controllers\CBController
{
	public function getIndex()
	{
		$Customers = Cache::remember('filtered_customers', 900, function() {
			return DB::connection('masterfile')
				->table('customer as cus')
				->select('cus.customer_code', 'cus.cutomer_name', 'cus.channel_id', 'cus.concept', 'cha.channel_description')
				->leftJoin('channels as cha', 'cus.channel_id', '=', 'cha.id')
				->where(function($query) {
					$query->where('cus.cutomer_name', 'like', '%FRA')
						->orWhere('cus.cutomer_name', 'like', '%RTL');
				})->get();
		});

		if (request()->ajax()) {
			
			$storeCustomer = request()->customer;
			$dateFrom = Carbon::parse(request()->dateFrom)->format('Ymd');
			$dateTo = Carbon::parse(request()->dateTo)->format('Ymd');
			$store = "'" . implode("','", $storeCustomer) . "'";

			$tender_data = Cache::remember("{$store}{$dateFrom}{$dateTo}", 900, function() use($store, $dateFrom, $dateTo){
			
			return DB::connection('sqlsrv')->select(DB::raw("
				SELECT 
					P.CreateDate AS 'DATE',
					P.CreateTime As 'TIME',
					P.WareHouse AS 'STORE ID',
					P.InvoiceNumber AS 'RECEIPT#',
					CM.CustomerName AS 'Name of Customer',
					SUM(P.LocalAmount) AS AMOUNT,
					CASE 
					WHEN P.PaymentType IN (0, 1) THEN PM.Description 
						ELSE 'Other Payment' 
					END AS TENDER,
					ISNULL(C.CustomerName, '') AS 'Credit Card Name',
					ISNULL(C.CreditCardNumber, '') AS 'Credit Card Number',
					ISNULL(C.CreditCardType, '') AS 'CARD TYPE',
					ISNULL(C.EDCName, '') AS EDC,
					ISNULL(C.ExpiryDate, '') AS EXPIRY,
					ISNULL(C.AuthorisationNumber, '') AS 'AP NO',
					PM.Description AS 'OP ID',
					P.ChangedBy AS [User],
					ISNULL(C.CommPer, '0') AS 'Commission %'
				FROM PaymentTrn P (NOLOCK)
				LEFT JOIN CreditCardInfo C (NOLOCK)
					ON P.Company = C.Company
					AND P.Division = C.Division
					AND P.WareHouse = C.WareHouse
					AND P.InvoiceNumber = C.InvoiceNumber
					AND P.InvoiceYear = C.InvoiceYear
				INNER JOIN PaymentMode PM (NOLOCK)
					ON P.Company = PM.Company
					AND P.WareHouse = PM.WareHouse
					AND P.PaymentType = PM.PaymentModeType
				INNER JOIN CashOrderTrn CT (NOLOCK)
					ON P.Company = CT.Company
					AND P.Division = CT.Division
					AND P.WareHouse = CT.WareHouse
					AND P.InvoiceNumber = CT.InvoiceNumber
					AND P.InvoiceYear = CT.InvoiceYear
					AND CT.InvoiceType = 30
				LEFT JOIN Customer CM (NOLOCK)
					ON CT.CustomerNumber = CM.CustomerNumber
					WHERE
					P.Company = 100
					AND P.Division = 100
					AND P.WareHouse in ($store)
					AND P.TransactionType = 1
					AND P.CreateDate BETWEEN $dateFrom AND $dateTo
				GROUP BY 
					P.CreateDate,
					P.CreateTime,
					P.WareHouse,
					P.InvoiceNumber,
					P.PaymentType,
					PM.Description,
					P.ChangedBy,
					C.CustomerName,
					C.CreditCardNumber,
					C.CommPer,
					C.CreditCardType,
					C.EDCName,
					C.ExpiryDate,
					C.AuthorisationNumber,
					CM.CustomerName"));
				});

				$customerMap = [];

				foreach ($Customers as $customer) {
					$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
				}

				foreach ($tender_data as $row) {
					$ammount = $row->AMOUNT; 
					$mdr = $row->{'Commission %'};

					$mdr_charge = ($mdr * $ammount);
					$net_amount = ($ammount - $mdr_charge);

					$customerCode = str_replace('CUS-', '', $row->{'STORE ID'});
					$row->customerName = $customerMap[$customerCode] ?? 'Unknown';
					$row->mdrCharge = $mdr_charge ?? 'Undefined';
					$row->netAmount = $net_amount ?? 'Undefined';
					$row->{'DATE'} = Carbon::parse($row->{'DATE'})->format('Y-m-d');
					$row->{'TIME'} = Carbon::parse($row->{'TIME'})->format('H:i:s');
				}

			return response()->json($tender_data);
		}

		$data = [];
		$data['page_title'] = 'Tender Report';
		$data['tender_data'] = [];
		$data['customers'] = $Customers;
		$data['channels'] = Channel::active();
		$data['concepts'] = Concept::active();
		$data['all_customers'] = DB::connection('masterfile')->table('customer')->select('customer_code', 'cutomer_name', 'concept')->get();

		return view('etp-pos.etp-tender-report', $data);
	}
}
