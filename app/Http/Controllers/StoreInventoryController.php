<?php

namespace App\Http\Controllers;

use DB;
use CRUDBooster;
use Carbon\Carbon;
use App\Models\Counter;
use Illuminate\Bus\Batch;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Models\StoreInventory;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Bus;
use App\Exports\StoreInventoryExcel;
use App\Models\StoreInventoryUpload;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Exports\StoreInventoryExport;
use App\Imports\StoreInventoryImport;
use App\Jobs\ExportStoreInventoryJob;
use Illuminate\Support\Facades\Cache;
use App\Models\StoreInventoriesReport;
use App\Rules\ExcelFileValidationRule;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\InventoryTransactionType;
use Illuminate\Support\Facades\Response;
use App\Jobs\AppendMoreStoreInventoryJob;
use App\Jobs\ProcessStoreInventoryUploadJob;
use App\Jobs\ExportStoreInventoryCreateFileJob;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

define('SUB_INVENTORY_GOOD', 'POS - GOOD');
define('SUB_INVENTORY_TRANSIT', 'POS - TRANSIT');
define('SUB_INVENTORY_RMA', 'POS - RMA');
define('SUB_INVENTORY_DEMO', 'POS - DEMO');


class StoreInventoryController extends Controller
{
    private $report_type;
    private $inventoryTypeCache = [];
    

    public function __construct(){
        $this->report_type = ['STORE INVENTORY','STORE INTRANSIT'];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreInventory  $storeInventory
     * @return \Illuminate\Http\Response
     */
    public function show(StoreInventory $storeInventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StoreInventory  $storeInventory
     * @return \Illuminate\Http\Response
     */
    public function edit(StoreInventory $storeInventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreInventory  $storeInventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreInventory $storeInventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreInventory  $storeInventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreInventory $storeInventory)
    {
        //
    }

    public function storeInventoryUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Store Inventory';
        $data['uploadRoute'] = route('store-inventory.upload');
        $data['uploadTemplate'] = route('store-inventory.template');
        $data['nextSeries'] = StoreInventory::getNextReference();
        return view('inventory.upload',$data);
    }

    public function storeInventoryUpload(Request $request)
    {
        $from_date = $request->get('inventory_date');
        $errors = array();
        $request->validate([
            'import_file' => ['required', 'file', new ExcelFileValidationRule(20)],
        ]);
        $time = time();
        $folder_name = "$time-" . Str::random(5);
        $folder_path = storage_path('app') . '/' . $folder_name;
        $excel_file_name = $request->import_file->getClientOriginalName();
        $excel_relative_path = $request->file('import_file')
            ->storeAs("store-inventory-upload/$folder_name", $excel_file_name, 'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        //check headings
        $header = config('excel-template-headers.store-inventory');

        for ($i = 0; $i < sizeof($headings); $i++) {
            if (!in_array($headings[$i], $header)) {
                $unMatch[] = $headings[$i];
            }
        }

        $batch_number = $time;

        if(!empty($unMatch)) {
            return redirect(route('store-inventory.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
        }
        $args = [
            'batch_number' => $batch_number,
            'excel_path' => $excel_path,
            'report_type' => $this->report_type,
            'folder_name' => $folder_name,
            'file_name' => $excel_file_name,
            'created_by' => CRUDBooster::myId(),
            'from_date' => $from_date,
        ];

        ProcessStoreInventoryUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {

        $batch_details = Bus::findBatch($batch_id);
        $upload_details = StoreInventoryUpload::where('job_batches_id', $batch_id)->first();
        $count = StoreInventory::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.store-inventory');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'store-inventory-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    // public function exportInventory(Request $request) {
    //     $filename = $request->input('filename').'.csv';

    //     $filters = $request->all();
  
    //     $storeInventoryCount = StoreInventory::filterForReport(StoreInventory::generateReport(), $filters)
    //         ->where('is_final', 1)->count();
    //     if($storeInventoryCount == 0){
    //         return response()->json(['msg'=>'Nothing to export','status'=>'error']);
    //     }
    //     $chunkSize = 10000;
    //     $numberOfChunks = ceil($storeInventoryCount / $chunkSize);
    //     $folder = 'store-inventory'.'-'.now()->toDateString() . '-' . str_replace(':', '-', now()->toTimeString()) . '-' . CRUDBooster::myId();
    //     $batches = [
    //         new ExportStoreInventoryCreateFileJob($chunkSize, $folder, $filters, $filename)
    //     ];

    //     if ($storeInventoryCount > $chunkSize) {
    //         $numberOfChunks = $numberOfChunks - 1;
    //         for ($numberOfChunks; $numberOfChunks > 0; $numberOfChunks--) {
    //             $batches[] = new AppendMoreStoreInventoryJob($numberOfChunks, $chunkSize, $folder, $filters, $filename);
    //         }
    //     }
    
    //     $batch = Bus::batch($batches)
    //         ->name('Export Store Inventory')
    //         ->then(function (Batch $batch) use ($folder) {
    //             $path = "exports/{$folder}/ExportStoreInventory.csv";
    //             // upload file to s3
    //             $file = storage_path("app/{$folder}/ExportStoreInventory.csv");
    //             Storage::disk('s3')->put($path, file_get_contents($file));
    //             // send email to admin
    //         })
    //         ->catch(function (Batch $batch, Throwable $e) {
    //             // send email to admin or log error
    //         })
    //         ->finally(function (Batch $batch) use ($folder) {
    //             // delete local file
    //             // Storage::disk('local')->deleteDirectory($folder);
    //         })
    //         ->dispatch();

    //     session()->put('lastStoreInventoryBatchId',$batch->id);
    //     session()->put('folderStoreInventory',$folder);
    //     // session()->put('filename',$filename);

    //     return [
    //         'batch_id' => $batch->id,
    //         'folder' => $folder,
    //         'status'   => 'success',
    //         'msg'      => 'Success'
    //     ];
        
    // }

    public function exportInventory(Request $request) {
        $filename = $request->input('filename').'.tsv';
        $filters = $request->all();
        $userReport = ReportPrivilege::myReport(3,CRUDBooster::myPrivilegeId());
        $query = StoreInventory::filterForReport(StoreInventory::generateReport(), $filters)
        ->where('is_final', 1);
        $headers = [
            'Content-Type' => 'text/tab-separated-values',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
  
        // Open a stream to write the response body
        $callback = function () use($userReport, $query) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($handle, explode(",",$userReport->report_header), "\t"); // Specify column names

            // Query and stream data
            
            $query->chunk(10000, function ($data) use ($handle, $userReport) {
                $sales = explode("`,`",$userReport->report_query);
                foreach($data as $value_data){
                    $salesData = [];
                    foreach ($sales as $key => $value) {
                        array_push($salesData, $value_data->$value);
                    }
                    fputcsv($handle,$salesData, "\t");
                }
            });
            
            fclose($handle);
        };
    
        // Return the streamed response
        return Response::stream($callback, 200, $headers);
    }

    public function progressExport(Request $request){
        try{
            $batchId = $request->batchId ?? session()->get('lastStoreInventoryBatchId');
            if(DB::table('job_batches')->where('id', $batchId)->count()){
                $response = DB::table('job_batches')->where('id', $batchId)->first();
                return response()->json($response);
            }
        }catch(Exception $e){
            Log::error($e);
            dd($e);
        }
    }

    public function StoresInventoryFromPosEtp(Request $request){
        $datefrom = $request->datefrom ? date("Ymd", strtotime($request->datefrom)) : date("Ymd", strtotime("-5 hour"));
        $dateto = $request->dateto ? date("Ymd", strtotime($request->dateto)) : date("Ymd", strtotime("-1 hour"));

        $firstQueryResult  = StoreInventory::scopeGetStoresInventoryFromPosEtp($datefrom, $dateto);
        $secondQueryResult = StoreInventory::scopeGetInTransitInventoryFromPosEtp($datefrom, $dateto);

        $mergedResults  = collect($firstQueryResult)->merge($secondQueryResult);

        
        StoreInventory::syncOldEntriesFromNewEntries($datefrom, $dateto, $mergedResults);


        $groupedSalesData = $mergedResults->groupBy(function ($item) {
            return $item->SubInventory === 'DEMO' ? 'DEMO' : 'NOT DEMO';
        });
        
        $demoItemNumbers = $groupedSalesData->get('DEMO', collect())
        ->pluck('ItemNumber')
        ->map(function ($itemNumber) {
            return str_replace(['Q1_', 'Q2_'], '', $itemNumber);
        })
        ->toArray();

        $notDemoItemNumbers = $groupedSalesData->get('NOT DEMO', collect())
        ->pluck('ItemNumber')
        ->map(function ($itemNumber) {
            return str_replace(['Q1_', 'Q2_'], '', $itemNumber);
        })
        ->toArray();
        
        $itemNumbers = collect($demoItemNumbers)
        ->merge($notDemoItemNumbers)
        ->unique()
        ->values()
        ->toArray();

        $itemDetails = $this->fetchItemDataInBatch($itemNumbers);

        $warehouseCodes = collect($firstQueryResult)
        ->merge($secondQueryResult) // Merge results from both queries
        ->flatMap(function($storeData) {
            $warehouseCode = !empty($storeData->StoreId) ? "CUS-" . $storeData->StoreId : '';
            $toWarehouseCode = !empty($storeData->ToWarehouse) ? "CUS-" . $storeData->ToWarehouse : '';

            return [$warehouseCode, $toWarehouseCode];
        })
        ->filter() // Remove empty values
        ->unique() // Remove duplicates
        ->toArray();

        $masterfile = DB::connection('masterfile')->table('customer')
            ->whereIn('customer_code', $warehouseCodes)
            ->get()
            ->keyBy('customer_code');
        

        $groupedByStoreId = $groupedSalesData->map(function ($items) {
            return $items->groupBy('StoreId');
        });

        foreach($groupedByStoreId as $itemKey => $item){

            foreach ($item as $storeId => $storeData) { 
                $time = microtime(true);
                $batch_number = str_replace('.', '', $time);;
                $folder_name = "$batch_number-" . Str::random(5);
                $dateNow = Carbon::now()->format('Ymd');
                $excel_file_name = "stores-inventory-$batch_number-$dateNow.xlsx";
                $excel_path = "store-inventory-upload/$folder_name/$excel_file_name";
        
                if (!file_exists(storage_path("app/store-inventory-upload/$folder_name"))) {
                    mkdir(storage_path("app/store-inventory-upload/$folder_name"), 0777, true);
                }
                $toExcelContent = [];  
                
                // Create Excel Data
                foreach($storeData as &$excel){

                    $itemNumber = str_replace(['Q1_', 'Q2_'], '', $excel->ItemNumber);
                    $sub_inventory = $this->getSubInventory($excel->ItemNumber, $excel, $itemKey);
                    $cusCode = "CUS-" . $excel->StoreId;

                    if (StoreInventory::isNotExist($excel, $masterfile[$cusCode]->cutomer_name, $itemNumber, $sub_inventory)) {
                        $counter = Counter::where('id', 2)->value('reference_code');
                        $toWarehouseCode = "CUS-" . $excel->ToWarehouse;
                        $toWareHouse = $masterfile[$toWarehouseCode]->warehouse_name ?? null;
                        $fromWareHouse = $masterfile[$cusCode]->warehouse_name ?? null;

                        $toExcel = [];
                        $toExcel['reference_number'] = $counter;
                        $toExcel['system'] = 'POS';
                        $toExcel['org'] = $itemDetails[$itemNumber]['org'];
                        $toExcel['report_type'] = 'STORE INVENTORY';
                        $toExcel['channel_code'] = $masterfile[$cusCode]->channel_code_id;
                        $toExcel['sub_inventory'] = $sub_inventory;
                        $toExcel['customer_location'] = $masterfile[$cusCode]->cutomer_name;
                        $toExcel['inventory_as_of_date'] = Carbon::createFromFormat('Ymd', $excel->Date)->format('Y-m-d');
                        $toExcel['item_number'] = $itemNumber;
                        $toExcel['item_description'] = $itemDetails[$itemNumber]['item_description'];
                        $toExcel['total_qty'] = $excel->TotalQty;
                        $toExcel['store_cost'] = $itemDetails[$itemNumber]['store_cost'];
                        $toExcel['store_cost_eccom'] = $itemDetails[$itemNumber]['store_cost_eccom'];
                        $toExcel['landed_cost'] = $itemDetails[$itemNumber]['landed_cost'];
                        $toExcel['product_quality'] = $this->productQuality($itemDetails[$itemNumber]['inventory_type_id'], $sub_inventory);
                        if (substr($excel->ItemNumber, 0, 3) === 'Q1_') {
                            $toExcel['from_warehouse'] = null;
                        } else {
                            $toExcel['from_warehouse'] = $fromWareHouse;
                        }
                        $toExcel['to_warehouse'] = $toWareHouse;
                        
                        $toExcelContent[] = $toExcel;
                        Counter::where('id',2)->increment('reference_code');
                    }

                }

                if(!empty($toExcelContent)){
                    
                    Excel::store(new StoreInventoryExcel($toExcelContent), $excel_path, 'local');
    
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
                        'created_by' => CRUDBooster::myId(),
                        'from_date' => null,
                        'data_type' => 'PULL'
                    ];
        
                    // Dispatch the processing job for each store
                    ProcessStoreInventoryUploadJob::dispatch($args);
                }
                
            }
        }

    }

    private function productQuality($inv_type_id, $pos_sub)
    {
        $item = null;

        if(isset($this->inventoryTypeCache[$inv_type_id])){
            $item = $this->inventoryTypeCache[$inv_type_id];
        }else{
            $item = DB::connection('imfs')->table('inventory_types')->where('id', $inv_type_id)->first();
            $this->inventoryTypeCache[$inv_type_id] = $item;
        }

        if(!$item){
            return null;
        }

        $inv_type = $item->inventory_type_description;

        if ($inv_type === 'ANY' && $pos_sub === SUB_INVENTORY_RMA) {
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

    function prepareItemData($item, $orgName, $ecomStoreMargin = 0) {
        return [
            'org' => $orgName,
            'item_description' => $item->item_description,
            'store_cost' => $item->dtp_rf,
            'store_cost_eccom' => $ecomStoreMargin,
            'landed_cost' => $item->landed_cost,
            'inventory_type_id' => $item->inventory_types_id
        ];
    }
    
    // function fetchItemDataInBatch($itemNumbers)
    // {
    //     $results = [];
    //     $itemNumberSet = collect($itemNumbers)->unique();

    //     // Define a cache key based on the unique item numbers
    //     $cacheKey = 'item_data_' . md5(implode(',', $itemNumberSet->toArray()));

    //     // Check if the data is already cached
    //     $cachedResults = Cache::get($cacheKey);

    //     if ($cachedResults) {
    //         return $cachedResults;
    //     }

    //     $itemMasterRecords = DB::connection('imfs')
    //     ->table('item_masters') 
    //     ->whereIn('digits_code', $itemNumberSet)
    //     ->get()
    //     ->keyBy('digits_code');

    //     foreach ($itemMasterRecords as $record) {
    //         $results[$record->digits_code] = $this->prepareItemData($record, 'DIGITS', $record->ecom_store_margin);
    //     }

    //     $foundItemNumbers = collect(array_keys($results));
    //     $missingItemNumbers = $itemNumberSet->diff($foundItemNumbers); 

    //     if ($missingItemNumbers->isEmpty()) {
    //         Cache::put($cacheKey, $results, now()->addMinutes(60)); 
    //         return $results;
    //     }

    //     // Check the second database
    //     $rmaItemMasterRecords = DB::connection('imfs')
    //     ->table('rma_item_masters')
    //     ->whereIn('digits_code', $missingItemNumbers)
    //     ->get()
    //     ->keyBy('digits_code');

    //     foreach ($rmaItemMasterRecords as $record) {
    //         $results[$record->digits_code] = $this->prepareItemData($record, 'RMA');
    //     }

    //     // Check for missing item numbers again
    //     $foundItemNumbersAfterRMA = collect(array_keys($results));
    //     $missingItemNumbersAfterRMA = $missingItemNumbers->diff($foundItemNumbersAfterRMA); 

    //     if ($missingItemNumbersAfterRMA->isNotEmpty()) {
    //         $aimfsItemMasterRecords = DB::connection('aimfs')
    //             ->table('digits_imfs')
    //             ->whereIn('digits_code', $missingItemNumbersAfterRMA)
    //             ->get()
    //             ->keyBy('digits_code');

    //         foreach ($aimfsItemMasterRecords as $record) {
    //             $results[$record->digits_code] = $this->prepareItemData($record, 'ADMIN');
    //         }
    //     }

    //     Cache::put($cacheKey, $results, now()->addMinutes(60)); 

    //     return $results;
    // }

    function fetchItemDataInBatch($itemNumbers)
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

        // Fetch from the first database (item_masters)
        $itemMasterRecords = DB::connection('imfs')
            ->table('item_masters')
            ->whereIn('digits_code', $missingItemNumbers)
            ->get()
            ->keyBy('digits_code');

        foreach ($itemMasterRecords as $record) {
            $itemData = $this->prepareItemData($record, 'DIGITS', $record->ecom_store_margin);
            $results[$record->digits_code] = $itemData;
            $cacheBatch['item_data_' . $record->digits_code] = $itemData;
        }

        $foundItemNumbers = collect(array_keys($results));
        $stillMissingItemNumbers = $missingItemNumbers->diff($foundItemNumbers);

        // Fetch missing items from the RMA database
        if ($stillMissingItemNumbers->isNotEmpty()) {
            $rmaItemMasterRecords = DB::connection('imfs')
                ->table('rma_item_masters')
                ->whereIn('digits_code', $stillMissingItemNumbers)
                ->get()
                ->keyBy('digits_code');

            foreach ($rmaItemMasterRecords as $record) {
                $itemData = $this->prepareItemData($record, 'RMA');
                $results[$record->digits_code] = $itemData;
                $cacheBatch['item_data_' . $record->digits_code] = $itemData;
            }

            $foundItemNumbersAfterRMA = collect(array_keys($results));
            $missingItemNumbersAfterRMA = $stillMissingItemNumbers->diff($foundItemNumbersAfterRMA);

            // Fetch from the third database (aimfs)
            if ($missingItemNumbersAfterRMA->isNotEmpty()) {
                $aimfsItemMasterRecords = DB::connection('aimfs')
                    ->table('digits_imfs')
                    ->whereIn('digits_code', $missingItemNumbersAfterRMA)
                    ->get()
                    ->keyBy('digits_code');

                foreach ($aimfsItemMasterRecords as $record) {
                    $itemData = $this->prepareItemData($record, 'ADMIN');
                    $results[$record->digits_code] = $itemData;
                    $cacheBatch['item_data_' . $record->digits_code] = $itemData;
                }
            }
        }

        // Batch insert the missing items into the cache
        if (!empty($cacheBatch)) {
            Cache::putMany($cacheBatch, now()->addMinutes(60));
        }
    }

    function getSubInventory($itemNumber, $item, $itemKey)
    {
        if($itemKey == "DEMO"){
            return "POS - DEMO";
        }else{
            $prefix = substr($itemNumber, 0, 3);

        if ($prefix === 'Q1_' && $item->SubInventory === "GOOD") {
            return "POS - GOOD";
        } elseif ($prefix === 'Q2_') {
            if($item->ToWarehouse === '0312'){
                return "POS - RMA";
            }else{
                return "POS - TRANSIT";
            }
        }
        }
        
    }


}   