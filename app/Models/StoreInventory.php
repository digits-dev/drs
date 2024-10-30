<?php

namespace App\Models;

use CRUDBooster;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreInventory extends Model
{
    use HasFactory;

    protected $table = 'store_inventories';

    protected $fillable = [
        'batch_date',
        'batch_number',
        'is_valid',
        'is_final',
        'reference_number',
        'systems_id',
        'organizations_id',
        'report_types_id',
        'channels_id',
        'inventory_transaction_types_id',
        'employees_id',
        'customers_id',
        'customer_location',
        'inventory_date',
        'item_code',
        'digits_code',
        'item_description',
        'quantity_inv',
        'srp',
        'qtyinv_srp',
        'store_cost',
        'qtyinv_sc',
        // 'current_srp',
        // 'qtyinv_csrp',
        'dtp_rf',
        'qtyinv_sc',
        'landed_cost',
        'qtyinv_lc',
        'dtp_ecom',
        'qtyinv_ecom',
        'created_by',
        'updated_by',
    ];

    public function scopeGetNextReference($query) {
        $storeInventory = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $storeInventory->reference_number + 1;
    }

    public function filterForReport($query, $filters = [], $is_upload = false) {
        $search = $filters['search'];
        if ($filters['datefrom'] && $filters['dateto']) {
            $query->whereBetween('store_inventories.inventory_date', [$filters['datefrom'], $filters['dateto']]);
        }
        if ($filters['channels_id']) {
            $query->where('store_inventories.channels_id', $filters['channels_id']);
        }
        if ($filters['systems_id']) {
            $query->where('store_inventories.systems_id', $filters['systems_id']);
        }
        // if ($search)  {
        //     $search_filter = "
        //         store_inventories.reference_number like '%$search%' OR
        //         systems.system_name like '%$search%' OR
        //         report_types.report_type like '%$search%' OR
        //         channels.channel_name like '%$search%' OR
        //         customers.customer_name like '%$search%' OR
        //         concepts.concept_name like '%$search%' OR
        //         store_inventories.inventory_date LIKE '%$search%'            
        //     ";

        //     if ($is_upload) {
        //         $search_filter .= "
        //             OR customers.customer_name LIKE '%$search%'
        //             OR employees.employee_name LIKE '%$search%'
        //             OR store_inventories.item_code LIKE '%$search%'
        //             OR all_items.item_description LIKE '%$search%'
        //             OR store_inventories.item_description LIKE '%$search%'
        //             OR all_items.margin_category_description LIKE '%$search%'
        //             OR all_items.brand_description LIKE '%$search%'
        //             OR all_items.sku_status_description LIKE '%$search%'
        //             OR all_items.category_description LIKE '%$search%'
        //             OR all_items.margin_category_description LIKE '%$search%'
        //             OR all_items.vendor_type_code LIKE '%$search%'
        //             OR all_items.inventory_type_description LIKE '%$search%'
        //         ";
        //     }
        //     $query->whereRaw("($search_filter)");
        // }
        return $query;
    }

    public function generateReport($ids = null) {
        $query = StoreInventory::select(
            'store_inventories.id',
            'store_inventories.batch_number',
            'store_inventories.is_final',
            'store_inventories.reference_number',
            'store_inventories.from_warehouse AS from_warehouse',
            'store_inventories.to_warehouse AS to_warehouse',
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            'inventory_transaction_types.inventory_transaction_type',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            DB::raw('COALESCE(customers.bill_to, employees.bill_to) AS bill_to'),
            'concepts.concept_name AS concept_name',
            'store_inventories.inventory_date AS inventory_date',
            'store_inventories.item_code AS item_code',
            'store_inventories.item_description AS item_description',
            'all_items.item_code AS digits_code',
            DB::raw('COALESCE(all_items.item_description, store_inventories.item_description) AS imfs_item_description'),
            'all_items.upc_code AS upc_code',
            'all_items.upc_code2 AS upc_code2',
            'all_items.upc_code3 AS upc_code3',
            'all_items.upc_code4 AS upc_code4',
            'all_items.upc_code5 AS upc_code5',
            'all_items.brand_description AS brand_description',
            'all_items.category_description AS category_description',
            'all_items.margin_category_description AS margin_category_description',
            'all_items.vendor_type_code AS vendor_type_code',
            'all_items.vendor_name AS vendor_name',
            'all_items.inventory_type_description AS inventory_type_description',
            'all_items.sku_status_description AS sku_status_description',
            'all_items.brand_status AS brand_status',
            'store_inventories.quantity_inv AS quantity_inv',
            'all_items.current_srp AS current_srp',
            DB::raw('(
                all_items.current_srp *store_inventories.quantity_inv
             ) AS qtyinv_srp'),
            'store_inventories.store_cost AS store_cost',
            'store_inventories.qtyinv_sc AS qtyinv_sc',
            'store_inventories.dtp_rf AS dtp_rf',
            'store_inventories.qtyinv_rf AS qtyinv_rf',
            'store_inventories.landed_cost AS landed_cost',
            'store_inventories.qtyinv_lc AS qtyinv_lc',
            'store_inventories.dtp_ecom AS dtp_ecom',
            'store_inventories.qtyinv_ecom as qtyinv_ecom',
            'store_inventories.product_quality as product_quality'
        )
        ->leftJoin('systems', 'store_inventories.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'store_inventories.organizations_id', '=', 'organizations.id')
        ->leftJoin('inventory_transaction_types', 'inventory_transaction_types.id', '=','store_inventories.inventory_transaction_types_id')
        ->leftJoin('report_types', 'store_inventories.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'store_inventories.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'store_inventories.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'store_inventories.employees_id', '=', 'employees.id')
        ->leftJoin('all_items', 'store_inventories.item_code', '=', 'all_items.item_code');

        if (isset($ids)) {
            $query->whereIn('store_inventories.id', $ids);
        }
        return $query;
    }

    public function scopeGetYajraDefaultData($query){
        return $query->leftJoin('systems', 'store_inventories.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'store_inventories.organizations_id', '=', 'organizations.id')
        ->leftJoin('report_types', 'store_inventories.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'store_inventories.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'store_inventories.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'store_inventories.employees_id', '=', 'employees.id')
        ->select(
            'store_inventories.id',
            'store_inventories.batch_number',
            'store_inventories.is_final',
            'store_inventories.reference_number',
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS concept_name',
            'store_inventories.inventory_date AS inventory_date',
            'store_inventories.item_code AS item_code',
            'store_inventories.item_description AS item_description',
            'store_inventories.store_cost AS store_cost',
            'store_inventories.qtyinv_sc AS qtyinv_sc',
            'store_inventories.dtp_rf AS dtp_rf',
            'store_inventories.qtyinv_rf AS qtyinv_rf',
            'store_inventories.landed_cost AS landed_cost',
            'store_inventories.qtyinv_lc AS qtyinv_lc',
            'store_inventories.dtp_ecom AS dtp_ecom',
            'store_inventories.qtyinv_ecom as qtyinv_ecom',
        )->where('is_final', 1)->limit(10)->get();
    }

    //FROM ETP
    public function scopeGetInTransitInventoryFromPosEtp($dateFrom, $dateTo){
        $data = DB::connection('sqlsrv')->select(DB::raw("
            SELECT d.Company, d.Warehouse AS 'StoreId', d.ToWarehouse, d.TransactionDate As 'Date',
                CONCAT('Q2_', l.ItemNumber) AS 'ItemNumber', l.ItemDescription, l.LotNumber, 
                l.DispatchedQuantity, l.ReceivedQuantity, 
                l.DispatchedQuantity - l.ReceivedQuantity AS TotalQty
            FROM DOHead (nolock) d
            INNER JOIN doline L 
                ON d.Company = l.Company 
                AND d.Division = l.Division 
                AND d.Warehouse = l.Warehouse 
                AND d.OrderNumber = l.OrderNumber
            WHERE d.Status = 2
            AND d.TransactionStatus IN (0, 2)   
            AND d.Company = 100
            AND d.Division = '100'
            AND (d.ToWarehouse = '0312' OR d.ToWarehouse = '0311')
            AND d.TransactionDate between '$dateFrom' and '$dateTo'
        "));
        
        return $data;
    }

    public function scopeGetStoresInventoryFromPosEtp($dateFrom, $dateTo){
        $data = DB::connection('sqlsrv')->select(DB::raw("
            select P.WareHouse 'StoreId',
            P.LastIssueDate 'Date',
            CONCAT('Q1_', P.ItemNumber) AS 'ItemNumber',
            P.BalanceApproved 'TotalQty',
            P.Location 'SubInventory'
            From ProductLocationBalance P (Nolock)
            where P.Company= 100
            AND (P.Location = 'GOOD' OR P.Location = 'DEMO')
            AND P.LastIssueDate between '$dateFrom' and '$dateTo'
        "));

        return $data;
    }

    public static function isNotExist($date, $totalQty, $customerLocation, $itemNumber, $subInventory){

        $customer = Customer::active();
        $subInventoryTb = InventoryTransactionType::active();

        try {
            $inventoryDate = Carbon::createFromFormat('Ymd', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return true; 
        }

        $v_customer = $customer->where('customer_name',trim($customerLocation))->first();
        $v_sub_inventory = $subInventoryTb->where('inventory_transaction_type', trim($subInventory))->first();

        if (!$v_customer) {
            return true;
        }

        return !self::where('customers_id', $v_customer->id)
            ->where('inventory_date', $inventoryDate)
            ->where('item_code', $itemNumber)
            ->where('quantity_inv', $totalQty)
            ->where('inventory_transaction_types_id', $v_sub_inventory->id)
            ->exists();
    }

    public static function syncOldEntriesFromNewEntries($dateFrom, $dateTo, $newEntries) {
        $oldData = self::fetchNewInventoryData($dateFrom, $dateTo);
        

        if(!empty($oldData)){
            $subInv = [];
            $newDatasKeys =[];
            $uniqueItems = [];

            foreach ($newEntries as $item) {
                $key = $item->StoreId . '-' . $item->Date . '-' . $item->ItemNumber . '-' . $item->SubInventory;
                
                if (isset($uniqueItems[$key])) {
                    $uniqueItems[$key]->TotalQty += $item->TotalQty;
                } else {
                    $uniqueItems[$key] = $item;
                }
            }

            $uniqueItems = array_values($uniqueItems);

            $uniqueOldSubInventoryId = collect($oldData)
            ->pluck('inventory_transaction_types_id') 
            ->filter(function ($value) {
                return !is_null($value); 
            })
            ->unique();
    
    
            $subInventory = InventoryTransactionType::whereIn('id', $uniqueOldSubInventoryId)->get();
    
            if ($subInventory->isNotEmpty()) {
                foreach ($subInventory as $sub) {
                    $subInv[$sub->id] = [
                        'name' => $sub->inventory_transaction_type,
                    ];
                }   
            }
    
            $uniqueOldCustomerIds = collect($oldData)->pluck('customers_id')->unique();
            $cusNames = [];
    
            $customers = Customer::whereIn('id', $uniqueOldCustomerIds)->get();
    
            if ($customers->isNotEmpty()) {
                foreach ($customers as $customer) {
                    $cusNames[$customer->id] = [
                        'name' => $customer->customer_name,
                    ];
                }
                
            }
    
            $customerNameCache = [];
    
            foreach ($uniqueItems as $entry) {
                $subInventory = '';
                $date = Carbon::createFromFormat('Ymd', $entry->Date)->format('Y-m-d');
    
                if(isset($customerNameCache[$entry->StoreId])){
                    $customerName = $customerNameCache[$entry->StoreId];
                }else{
                    $customerName = DB::connection('masterfile')->table('customer')
                    ->where('customer_code', "CUS-" . $entry->StoreId)
                    ->pluck('cutomer_name')
                    ->first();
    
                    $customerNameCache[$entry->StoreId] = $customerName;
                }
                
                $itemNumber = str_replace(['Q1_', 'Q2_'], '', $entry->ItemNumber);
                
                $prefix = substr($entry->ItemNumber, 0, 3);
        
                if ($prefix === 'Q1_') {
                    if($entry->SubInventory === "GOOD" || $entry->SubInventory === "DEMO"){
                        $subInventory = "POS - " . $entry->SubInventory;
                    }else{
                        $subInventory = '';
                    }
                } elseif ($prefix === 'Q2_') {
                    if($entry->ToWarehouse === '0312'){
                        $subInventory = "POS - RMA";
                    }else{
                        $subInventory = "POS - TRANSIT";
                    }
                }   
    
                // $key = $customerName . '-' . $date . '-' . $itemNumber . '-' . intval($entry->TotalQty) . '-' . $subInventory;
                
                // $newDatasKeys[$key] = $entry;
                
                //-----------------------------
                // $entryArray = (array)$entry;

                // $newDatasKeys[$key] = $entryArray;

                // if (isset($newDatasKeys[$key])) {
                //     $newDatasKeys[$key][] = $entryArray;
                // } else {
                //     $newDatasKeys[$key] = $entryArray;
                // }

                $key = $customerName . '-' . $date . '-' . $itemNumber . '-' . $subInventory;

                $newDatasKeys[$key] = $entry;

            }  
                
            $idsToUpdate = [];

            foreach ($oldData as $entry) {
                // $key = $cusNames[$entry['customers_id']]['name'] . '-' . $entry['inventory_date'] . '-' . $entry['item_code'] . '-' . $entry['quantity_inv'] . '-' . $subInv[$entry['inventory_transaction_types_id']]['name'];
                $key = $cusNames[$entry['customers_id']]['name'] . '-' . $entry['inventory_date'] . '-' . $entry['item_code'] . '-' . $subInv[$entry['inventory_transaction_types_id']]['name'];

                if (!isset($newDatasKeys[$key])) {
                    $idsToUpdate[] = $entry['id'];
                }else{
                    if($entry['quantity_inv'] !== intval($newDatasKeys[$key]->TotalQty)){
                        // dump("change qty for id no: " .  $entry['id']. " from: " . $entry['quantity_inv'] . " to: " . $newDatasKeys[$key]->TotalQty);
                        self::where('id', $entry['id'])->update(['quantity_inv' => $newDatasKeys[$key]->TotalQty]);
                    }
                }
            }

            // dump('ids need to zero out: ' . implode(', ', $idsToUpdate));
            if (!empty($idsToUpdate)) {
                self::whereIn('id', $idsToUpdate)->update(['quantity_inv' => 0]);
            }

        }

    }

    private static function fetchNewInventoryData($dateFrom, $dateTo) {
        return self::whereBetween('inventory_date', [$dateFrom, $dateTo])->get()->toArray();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->created_by = CRUDBooster::myId();
            }else{
                $model->created_by = 136;
            }
        });
        static::updating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->updated_by = CRUDBooster::myId();
            }else{
                $model->created_by = 136;
            }
        });
    }
}