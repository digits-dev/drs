<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtpStoreSyncReportController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function getIndex()
	{

		$data = [];
		$data['page_title'] = 'Store Sync';

		return view('etp-pos.etp-storesync-page', $data);
	}

	public function getStoreSync(){
		$customerMap = [];
		$storesMap = [];
		$inventoryMap = [];
		$customers_masterfile = Cache::remember('filtered_customers', 900, function() {
			return DB::connection('masterfile')->table('customer')
				->select('customer_code', 'cutomer_name', 'concept')
				->where(function ($query) {
					$query->where('cutomer_name', 'like', '%FRA')
						->orWhere('cutomer_name', 'like', '%RTL');})
				->get();
			});

		$store_sales = Cache::remember('filtered_store_sales', 900, function() {
			return DB::table('store_sales')
				->select('customer_name', DB::raw('MAX(sales_date) as latest_sales_date'))
				->leftJoin('customers', 'store_sales.customers_id', '=', 'customers.id')
				->groupBy('customer_name')
				->orderBy('latest_sales_date', 'desc')
				->get();
			});

		$store_inventories = Cache::remember('filtered_store_inventories', 900, function() {
			return DB::table('store_inventories')
				->select('customer_name', DB::raw('MAX(inventory_date) as latest_inventory_date'))
				->leftJoin('customers', 'store_inventories.customers_id', '=', 'customers.id')
				->groupBy('customer_name')
				->orderBy('latest_inventory_date', 'desc')
				->get();
			});

		foreach ($customers_masterfile as $customer) {
			$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
		}

		foreach ($store_sales as &$sales) {

			$customers_masterfile = Cache::rememberForever('StoreSync' . $sales->customer_name, function () use ($sales) {
				return DB::connection('masterfile')->table('customer')
					->where(function ($query) {
						$query->where('cutomer_name', 'like', '%FRA')
							->orWhere('cutomer_name', 'like', '%RTL');
					})
					->where('cutomer_name', $sales->customer_name)
					->first();
			});
			$storesMap[explode('-', $customers_masterfile->customer_code)[1]] = $sales->latest_sales_date;
		}

		foreach ($store_inventories as &$inventories) {

			$customers_masterfile = Cache::rememberForever('StoreSync' . $inventories->customer_name, function () use ($inventories) {
				return DB::connection('masterfile')->table('customer')
					->where(function ($query) {
						$query->where('cutomer_name', 'like', '%FRA')
							->orWhere('cutomer_name', 'like', '%RTL');
					})
					->where('cutomer_name', $inventories->customer_name)
					->first();
			});
			$inventoryMap[explode('-', $customers_masterfile->customer_code)[1]] = $inventories->latest_inventory_date;
		}

		$data = [];
		$data['page_title'] = 'Store Sync';
		$data['store_sync_data'] = Cache::remember('filtered_store_sync_data', 900, function() {
			return DB::connection('sqlsrv')
				->select(DB::raw("exec [SP_Custom_StoreSyncReport]"));
			});

		foreach ($data['store_sync_data'] as $row) {
			$row->store_last_sync =  Carbon::parse($storesMap[$row->Warehouse] ?? ' ')->format('Y-m-d');
			$row->inventory_last_sync =  Carbon::parse($inventoryMap[$row->Warehouse] ?? ' ')->format('Y-m-d');
			$row->Warehouse = $customerMap[$row->Warehouse] ?? ' ';
			$row->Date = Carbon::parse($row->Date)->format('Y-m-d');
			$row->Time = Carbon::createFromFormat('His', $row->Time)->format('h:i:s A');
		}

		return view('etp-pos.etp-storesync-report', $data);
	}
}
