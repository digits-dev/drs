<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Counter;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\DB;
use App\Exports\StoreInventoryExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use CRUDBooster;

define('SUB_INVENTORY_GOOD', 'POS - GOOD');
define('SUB_INVENTORY_TRANSIT', 'POS - TRANSIT');
define('SUB_INVENTORY_RMA', 'POS - RMA');
define('SUB_INVENTORY_DEMO', 'POS - DEMO');

class ProcessStoresInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dateFrom;
    protected $dateTo;
    private $inventoryTypeCache = [];
    private $report_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dateFrom, $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->report_type = ['STORE INVENTORY', 'STORE INTRANSIT'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $datefrom = $this->dateFrom;
        $dateto = $this->dateTo;

        $firstQueryResult = StoreInventory::scopeGetStoresInventoryFromPosEtp($datefrom, $dateto);
        $secondQueryResult = StoreInventory::scopeGetInTransitInventoryFromPosEtp($datefrom, $dateto);

        $mergedResults = collect($firstQueryResult)->merge($secondQueryResult);

        $mergedResultsCopy = collect($mergedResults->all())->map(function ($item) {
            return clone $item;
        });

        StoreInventory::syncOldEntriesFromNewEntries($datefrom, $dateto, $mergedResultsCopy);

        $groupedSalesData = $mergedResults->groupBy(function ($item) {
            return $item->SubInventory === 'DEMO' ? 'DEMO' : 'NOT DEMO';
        });

        $itemNumbers = $groupedSalesData
            ->flatMap(function ($items) {
                return $items->pluck('ItemNumber')->map(function ($itemNumber) {
                    return str_replace(['Q1_', 'Q2_'], '', $itemNumber);
                });
            })
            ->unique()
            ->values()
            ->toArray();

        $itemDetails = $this->fetchItemDataInBatch($itemNumbers);

        // dd($itemDetails);

        $warehouseCodes = $mergedResults
            ->flatMap(function ($storeData) {
                return [!empty($storeData->StoreId) ? 'CUS-' . $storeData->StoreId : null, !empty($storeData->ToWarehouse) ? 'CUS-' . $storeData->ToWarehouse : null];
            })
            ->filter()
            ->unique()
            ->toArray();

        $masterfile = DB::connection('masterfile')
            ->table('customer')
            ->select(['cutomer_name', 'channel_code_id', 'warehouse_name', 'customer_code'])
            ->whereIn('customer_code', $warehouseCodes)
            ->get()
            ->keyBy('customer_code');

        $groupedByStoreId = $groupedSalesData->map(function ($items) {
            return $items->groupBy('StoreId');
        });

        // dd($groupedByStoreId);

        foreach ($groupedByStoreId as $itemKey => $item) {
            foreach ($item as $storeId => $storeData) {
                $time = microtime(true);
                $batch_number = str_replace('.', '', $time);
                $folder_name = "$batch_number-" . Str::random(5);
                $dateNow = Carbon::now()->format('Ymd');
                $excel_file_name = "stores-inventory-$batch_number-$dateNow.xlsx";
                $excel_path = "store-inventory-upload/$folder_name/$excel_file_name";

                if (!file_exists(storage_path("app/store-inventory-upload/$folder_name"))) {
                    mkdir(storage_path("app/store-inventory-upload/$folder_name"), 0777, true);
                }
                $toExcelContent = [];
                $uniqueInventory = [];

                // Create Excel Data
                $excelData = $this->prepareExcelData($storeData, $itemKey, $itemDetails, $masterfile, $batch_number);

                // dd($excelData);
                foreach ($uniqueInventory as $item) {
                    $cusCode = 'CUS-' . $item['item']->StoreId;
                    $toWarehouse = $item['item']->ToWarehouse;
                    $itemKey = $item['itemKey'];
                    $subInv = $item['item']->SubInventory;
                    $index = $item['index'];
                    $itemNumber = str_replace(['Q1_', 'Q2_'], '', $item['item']->ItemNumber);

                    $sub_inventory = $this->getSubInventory($item['item']->ItemNumber, $toWarehouse, $itemKey, $subInv);

                    // if (!StoreInventory::isNotExist($item['item']->Date, $item['totalQty'], $masterfile[$cusCode]->cutomer_name, $itemNumber, $sub_inventory)) {
                    //     unset($toExcelContent[$index]);
                    // }
                }

                if (!empty($excelData)) {
                    Excel::store(new StoreInventoryExcel($excelData), $excel_path, 'local');

                    // ExportStoreInventoryJob::dispatch($toExcelContent, $excel_path);

                    // Full path of the stored Excel file
                    $full_excel_path = storage_path('app') . '/' . $excel_path;

                    // Prepare arguments for the job
                    $args = [
                        'batch_number' => $batch_number,
                        'excel_path' => $full_excel_path,
                        'report_type' => $this->report_type,
                        'folder_name' => $folder_name,
                        'file_name' => $excel_file_name,
                        'created_by' => null,
                        'from_date' => null,
                        'data_type' => 'PULL',
                    ];

                    // Dispatch the processing job for each store
                    ProcessStoreInventoryUploadJob::dispatch($args);
                }
            }
        }
    }

    protected function prepareExcelData($storeData, $itemKey, $itemDetails, $masterfile, $batchNumber)
    {
        $toExcelContent = [];
        $uniqueInventory = [];

        foreach ($storeData as $excel) {
            $itemNumber = str_replace(['Q1_', 'Q2_'], '', $excel->ItemNumber);

            if ($itemDetails[$itemNumber]['org'] !== 'RMA' && $itemDetails[$itemNumber]['org'] !== 'ACCOUNTING') {
                $sub_inventory = $this->getSubInventory($excel->ItemNumber, $excel->ToWarehouse, $itemKey, $excel->SubInventory);
                $cusCode = 'CUS-' . $excel->StoreId;
                $key = "{$excel->StoreId}{$excel->Date}{$excel->ItemNumber}" . ($excel->SubInventory ?? $sub_inventory);

                if (isset($uniqueInventory[$key])) {
                    $index = $uniqueInventory[$key]['index'];
                    $currQty = $uniqueInventory[$key]['totalQty'];
                    $toExcelContent[$index]['total_qty'] = $currQty + $excel->TotalQty;
                    $uniqueInventory[$key]['totalQty'] = $toExcelContent[$index]['total_qty'];
                } else {
                    // If key does not exist, create a new unique entry
                    $uniqueInventory[$key] = [
                        'index' => count($toExcelContent),
                        'totalQty' => $excel->TotalQty,
                        'itemKey' => $itemKey,
                        'item' => $excel,
                    ];

                    $refCounter = Counter::where('id', 2)->value('reference_code');

                    $toWarehouseCode = 'CUS-' . $excel->ToWarehouse;
                    $toWareHouse = $masterfile[$toWarehouseCode]->warehouse_name ?? null;
                    $fromWareHouse = $masterfile[$cusCode]->warehouse_name ?? null;
                    $org = $itemDetails[$itemNumber]['org'];
                    $reportType = 'STORE INVENTORY';
                    $channelCode = $masterfile[$cusCode]->channel_code_id;
                    $customerLoc = $masterfile[$cusCode]->cutomer_name;
                    $itemDescription = $itemDetails[$itemNumber]['item_description'];
                    $inventoryAsOfDate = Carbon::createFromFormat('Ymd', $excel->Date)->format('Y-m-d');
                    $storeCost = $itemDetails[$itemNumber]['store_cost'];

                    $productQuality = $this->productQuality($itemDetails[$itemNumber]['inventory_type_id'], $sub_inventory);

                    // Add entry to Excel data array
                    $toExcelContent[] = [
                        'reference_number' => $refCounter,
                        'system' => 'POS',
                        'org' => $org,
                        'report_type' => $reportType,
                        'channel_code' => $channelCode,
                        'sub_inventory' => $sub_inventory,
                        'customer_location' => $customerLoc,
                        'inventory_as_of_date' => $inventoryAsOfDate,
                        'item_number' => $itemNumber,
                        'item_description' => $itemDescription,
                        'total_qty' => $excel->TotalQty,
                        'store_cost' => $storeCost,
                        'store_cost_eccom' => $itemDetails[$itemNumber]['store_cost_eccom'],
                        'landed_cost' => $itemDetails[$itemNumber]['landed_cost'],
                        'product_quality' => $productQuality,
                        'from_warehouse' => $fromWareHouse,
                        'to_warehouse' => $toWareHouse,
                    ];

                    Counter::where('id', 2)->increment('reference_code');
                }
            }
        }

        return $toExcelContent;
    }

    protected function productQuality($inv_type_id, $pos_sub)
    {
        $item = null;

        if (isset($this->inventoryTypeCache[$inv_type_id])) {
            $item = $this->inventoryTypeCache[$inv_type_id];
        } else {
            $item = DB::connection('imfs')->table('inventory_types')->where('id', $inv_type_id)->first();
            $this->inventoryTypeCache[$inv_type_id] = $item;
        }

        if (!$item) {
            return null;
        }

        $inv_type = $item->inventory_type_description;

        if ($pos_sub === SUB_INVENTORY_RMA) {
            return 'DEFECTIVE';
        }

        if ($inv_type === 'HMR' && in_array($pos_sub, [SUB_INVENTORY_GOOD, SUB_INVENTORY_DEMO, SUB_INVENTORY_TRANSIT])) {
            return 'HMR';
        }

        if (in_array($inv_type, ['TRADE', 'MARKETING', 'STORE DEMO']) && in_array($pos_sub, [SUB_INVENTORY_GOOD, SUB_INVENTORY_DEMO, SUB_INVENTORY_TRANSIT])) {
            return 'GOOD';
        }

        return null;
    }

    protected function prepareItemData($item, $orgName, $ecomStoreMargin = 0)
    {
        return [
            'org' => $orgName,
            'item_description' => $item->item_description,
            'store_cost' => $item->dtp_rf,
            'store_cost_eccom' => $ecomStoreMargin,
            'landed_cost' => $item->landed_cost,
            'inventory_type_id' => $item->inventory_types_id,
        ];
    }

    protected function fetchItemDataInBatch($itemNumbers)
    {
        $results = [];
        $itemNumberSet = collect($itemNumbers)->unique();

        // Prepare cache keys for each item number
        $cacheKeys = $itemNumberSet->mapWithKeys(function ($itemNumber) {
            return ['item_data_' . $itemNumber => $itemNumber];
        });

        // Batch fetch all cached items
        $cachedItems = Cache::many($cacheKeys->keys()->toArray());

        // Separate found items from missing items
        $missingItemNumbers = collect();
        foreach ($cacheKeys as $cacheKey => $itemNumber) {
            if (isset($cachedItems[$cacheKey])) {
                $results[$itemNumber] = $cachedItems[$cacheKey];
            } else {
                $missingItemNumbers->push($itemNumber);
            }
        }

        // If there are missing items, fetch them from the databases
        if ($missingItemNumbers->isNotEmpty()) {
            $this->fetchAndCacheMissingItems($missingItemNumbers, $results);
        }

        return $results;
    }

    protected function fetchAndCacheMissingItems($missingItemNumbers, &$results)
    {
        $cacheBatch = [];
        $missingItemNumbers = collect($missingItemNumbers);

        $tables = [
            [
                'connection' => 'imfs',
                'table' => 'item_masters',
                'columns' => ['id', 'item_description', 'dtp_rf', 'landed_cost', 'inventory_types_id', 'digits_code'],
                'extra_conditions' => [],
                'type' => 'DIGITS',
            ],
            [
                'connection' => 'imfs',
                'table' => 'accounting_items',
                'columns' => ['id', 'item_description', 'digits_code'],
                'type' => 'ACCOUNTING',
            ],
            [
                'connection' => 'imfs',
                'table' => 'rma_item_masters',
                'columns' => ['id', 'item_description', 'dtp_rf', 'landed_cost', 'inventory_types_id', 'digits_code'],
                // 'extra_conditions' => ['rma_categories_id', '!=', 5],
                'extra_conditions' => [],
                'type' => 'RMA',
            ],
            [
                'connection' => 'aimfs',
                'table' => 'digits_imfs',
                'columns' => ['id', 'item_description', 'dtp_rf', 'landed_cost', 'digits_code'],
                'extra_conditions' => [],
                'type' => 'PURCHASINGG',
            ],
        ];

        foreach ($tables as $table) {
            if ($missingItemNumbers->isEmpty()) {
                break;
            }

            DB::connection($table['connection'])
                ->table($table['table'])
                ->select($table['columns'])
                ->whereIn('digits_code', $missingItemNumbers)
                ->when(!empty($table['extra_conditions']), function ($query) use ($table) {
                    [$field, $operator, $value] = $table['extra_conditions'];
                    return $query->where($field, $operator, $value);
                })
                ->chunkById(1000, function ($records) use (&$results, &$cacheBatch, &$missingItemNumbers, $table) {
                    foreach ($records as $record) {
                        $itemData = $this->prepareItemData($record, $table['type']);
                        $results[$record->digits_code] = $itemData;
                        $cacheBatch['item_data_' . $record->digits_code] = $itemData;
                    }

                    // Remove found items from the missing item numbers
                    $foundItemNumbers = collect($records)->pluck('digits_code');
                    $missingItemNumbers = $missingItemNumbers->diff($foundItemNumbers);
                });
        }

        // Cache the results in chunks of 1000
        if (!empty($cacheBatch)) {
            $cacheChunks = array_chunk($cacheBatch, 1000, true);
            foreach ($cacheChunks as $chunk) {
                Cache::putMany($chunk, now()->addMinutes(60));
            }
        }
    }

    protected function getSubInventory($itemNumber, $toWarehouse, $itemKey, $subInventory)
    {
        if ($itemKey == 'DEMO') {
            return 'POS - DEMO';
        } else {
            $prefix = substr($itemNumber, 0, 3);

            if ($prefix === 'Q1_' && $subInventory === 'GOOD') {
                return 'POS - GOOD';
            } elseif ($prefix === 'Q2_') {
                if ($toWarehouse === '0312') {
                    return 'POS - RMA';
                } else {
                    return 'POS - TRANSIT';
                }
            }
        }
    }
}
