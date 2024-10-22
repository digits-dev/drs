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

	public function getStoreSync()
	{
		$customerMap = [];
		$storesMap = [];
		$inventoryMap = [];

		$customers_masterfile = Cache::remember('filtered_customers', 900, function() {
			return DB::connection('masterfile')->table('customer')
				->select('customer_code', 'cutomer_name', 'concept')
				->where(function ($query) {
					$query->where('cutomer_name', 'like', '%FRA')
						->orWhere('cutomer_name', 'like', '%RTL');
				})
				->get();
		});

		foreach ($customers_masterfile as $customer) {
			$customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
		}

		$store_sales =  DB::table('store_sales')
				->select('customer_name', DB::raw('MAX(sales_date) as latest_sales_date'))
				->leftJoin('customers', 'store_sales.customers_id', '=', 'customers.id')
				->groupBy('customer_name')
				->orderBy('latest_sales_date', 'desc')
				->get();
		

		$store_inventories = DB::table('store_inventories')
				->select('customer_name', DB::raw('MAX(inventory_date) as latest_inventory_date'))
				->leftJoin('customers', 'store_inventories.customers_id', '=', 'customers.id')
				->groupBy('customer_name')
				->orderBy('latest_inventory_date', 'desc')
				->get();

		$customerNames = array_merge(
			$store_sales->pluck('customer_name')->toArray(),
			$store_inventories->pluck('customer_name')->toArray()
		);

		$customerData = Cache::remember('StoreSync_batch', 3600, function () use ($customerNames) {
			return DB::connection('masterfile')->table('customer')
				->whereIn('cutomer_name', $customerNames)
				->get()
				->keyBy('cutomer_name');
		});

		foreach ($store_sales as $sales) {
			if (isset($customerData[$sales->customer_name])) {
				$customer = $customerData[$sales->customer_name];
				$storesMap[explode('-', $customer->customer_code)[1]] = $sales->latest_sales_date;
			}
		}

		foreach ($store_inventories as $inventories) {
			if (isset($customerData[$inventories->customer_name])) {
				$customer = $customerData[$inventories->customer_name];
				$inventoryMap[explode('-', $customer->customer_code)[1]] = $inventories->latest_inventory_date;
			}
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
