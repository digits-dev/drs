<?php

namespace App\Http\Controllers;

use App\Models\StoreSale;
use App\Models\StoreSalesReport;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreSalesExport;
use App\Exports\StoreTestExportBatches;
use App\Exports\StoreSalesExcel;
use App\Imports\StoreSalesImport;
use App\Jobs\ProcessStoreSalesUploadJob;
use App\Jobs\StoreSalesImportJob;
use App\Models\ReportType;
use App\Models\StoreSalesUpload;
use App\Models\StoreSalesUploadLine;
use App\Rules\ExcelFileValidationRule;
use App\Jobs\ExportStoreSalesCreateFileJob;
use App\Jobs\AppendMoreStoreSalesJob;
use Carbon\Carbon;
use CRUDBooster;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use DB;
use App\Mail\SendSalesToEmail;
use Mail;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Response;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Counter;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class StoreSaleController extends Controller
{
    private $report_type;
    public $batchId;
    private $userReport;
    private $customer;
    public function __construct(){
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
        $this->report_type = ['STORE SALES'];
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $this->customer = Customer::active();
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
     * @param  \App\Models\StoreSale  $storeSale
     * @return \Illuminate\Http\Response
     */
    public function show(StoreSale $storeSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StoreSale  $storeSale
     * @return \Illuminate\Http\Response
     */
    public function edit(StoreSale $storeSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreSale  $storeSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreSale $storeSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreSale  $storeSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreSale $storeSale)
    {
        //
    }

    public function storeSalesUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Store Sales';
        $data['uploadRoute'] = route('store-sales.upload');
        $data['uploadTemplate'] = route('store-sales.template');
        $data['nextSeries'] = StoreSale::getNextReference();
        return view('sales.upload',$data);
    }

    public function storeSalesUpload(Request $request)
    {
        [$from_date, $to_date] = explode(' - ', $request->get('sales_date'));
        $errors = array();
        $request->validate([
            'import_file' => ['required', 'file', new ExcelFileValidationRule(20)],
        ]);
        $time = time();
        $folder_name = "$time-" . Str::random(5);
        $folder_path = storage_path('app') . '/' . $folder_name;
        $excel_file_name = $request->import_file->getClientOriginalName();
        $excel_relative_path = $request->file('import_file')
            ->storeAs("store-sales-upload/$folder_name", $excel_file_name, 'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        // // Count rows in the Excel file
        // $excelDataCount = Excel::toArray(null, $excel_path);
        // $row_count = count($excelDataCount[0]); // Assuming the first sheet is used
        // $row_count_without_header = $row_count - 1;

        // $counter = Counter::find(1);
        // $counter->reference_code += $row_count_without_header;
        // $counter->save();

        //check headings
        $header = config('excel-template-headers.store-sales');

        for ($i = 0; $i < sizeof($headings); $i++) {
            if (!in_array($headings[$i], $header)) {
                $unMatch[] = $headings[$i];
            }
        }

        $batch_number = $time;

        if (!empty($unMatch)) {
            return redirect(route('store-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
        }

        $args = [
            'batch_number' => $batch_number,
            'excel_path' => $excel_path,
            'report_type' => $this->report_type,
            'folder_name' => $folder_name,
            'file_name' => $excel_file_name,
            'created_by' => CRUDBooster::myId(),
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];

        ProcessStoreSalesUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {

        $batch_details = Bus::findBatch($batch_id);
        $upload_details = StoreSalesUpload::where('job_batches_id', $batch_id)->first();
        $count = StoreSale::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.store-sales');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'store-sales-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

  
    public function exportSales(Request $request) {
        $filename = $request->input('filename').'.tsv';
        $filters = $request->all();
        $userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $query = StoreSale::filterForReport(StoreSale::generateReport(), $filters)
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

    //PULL FROM ETP
    public function StoresSalesFromPosEtp(Request $request){
        $datefrom = $request->datefrom ? date("Ymd", strtotime($request->datefrom)) : date("Ymd", strtotime("-5 hour"));
        $dateto = $request->dateto ? date("Ymd", strtotime($request->dateto)) : date("Ymd", strtotime("-1 hour"));
      
        $result = StoreSale::getStoresSalesFromPosEtp($datefrom,$dateto);
        // Group sales data by store ID
        $groupedSalesData = collect($result)->groupBy('STORE ID');
        foreach ($groupedSalesData as $storeId => $storeData) {
            $time = microtime(true);
            $batch_number = str_replace('.', '', $time);
            $folder_name = "$batch_number-" . Str::random(5);
            $dateNow = Carbon::now()->format('Ymd');
            $excel_file_name = "stores-sales-$batch_number-$dateNow.xlsx";
            $excel_path = "store-sales-upload/$folder_name/$excel_file_name";
    
            if (!file_exists(storage_path("app/store-sales-upload/$folder_name"))) {
                mkdir(storage_path("app/store-sales-upload/$folder_name"), 0777, true);
            }
            $toExcelContent = [];
            // Initialize the cache arrays
            $masterfileCache = [];

            foreach($storeData as $item){
                $itemNumbers[] = $item->{'ITEM NUMBER'};
            }

            $itemDetails = self::fetchItemDataInBatch($itemNumbers);
    
            foreach ($storeData as &$excel) {
                $counter = Counter::where('id', 1)->value('reference_code');
                $modified = [];
                foreach ($excel as $key => $value) {
                    // Replace spaces with underscores in keys
                    $newKey = str_replace(' ', '_', $key);
                    $modified[$newKey] = $value;
                }
                $excel = $modified;
                $itemNumber = $excel['ITEM_NUMBER'];
      
                // MASTERFILE CACHING
                $cusCode = "CUS-" . $excel['STORE_ID'];
                if (isset($masterfileCache[$cusCode])) {
                    // Retrieve from cache if exists
                    $masterfile = $masterfileCache[$cusCode];
                } else {
                    // Query the database and store in cache
                    $masterfile = DB::connection('masterfile')->table('customer')->where('customer_code', $cusCode)->first();
                    $masterfileCache[$cusCode] = $masterfile;
                }
                $sales_date = Carbon::createFromFormat('Ymd', $excel['SOLD_DATE'])->format('Y-m-d');
                $v_customer = $this->customer->where('customer_name',$masterfile->cutomer_name)->first();
                $receipt_number = $excel['RECEIPT_#'];
                $item_serial = $excel['ITEM_SERIAL'];
                $qty_sold = $excel['QTY_SOLD'];
                $sold_price = $excel['SOLD_PRICE'];
                $isExistInStoreSales = StoreSale::where('customers_id', $v_customer->id ?? null)
                                                 ->where('receipt_number', $receipt_number)
                                                 ->where('item_code', $itemNumber)
                                                 ->where('sales_date', $sales_date)
                                                //  ->where('is_final',1)
                                                 ->orWhere('item_serial', $item_serial)
                                                 ->get(['customers_id', 'receipt_number', 'item_code', 'sales_date', 'item_serial'])
                                                 ->keyBy(function ($item) {
                                                     return $item->customers_id . '-' . $item->receipt_number . '-' . $item->item_code . '-' . $item->sales_date . '-' . $item->item_serial;
                                                 });
                $key = "{$v_customer->id}-{$receipt_number}-{$itemNumber}-{$sales_date}-{$item_serial}";
                if (!isset($isExistInStoreSales[$key])) {
                    // Prepare data for output
                    $toExcel = [];
                    $toExcel['reference_number'] = $counter;
                    $toExcel['system'] = 'POS';
                    $toExcel['org'] = $itemDetails[$itemNumber]['org'];
                    $toExcel['report_type'] = 'STORE SALES';
                    $toExcel['channel_code'] = $masterfile->channel_code_id;
                    $toExcel['customer_location'] = $masterfile->cutomer_name;
                    $toExcel['receipt_number'] = $receipt_number;
                    $toExcel['sold_date'] = $sales_date;
                    $toExcel['item_number'] = $itemNumber;
                    $toExcel['item_description'] = $itemDetails[$itemNumber]['item_description'];
                    //$toExcel['sold_price'] = $sold_price - ($excel['Discount_32'] + $excel['Discount_35']);
                    $toExcel['qty_sold'] = $qty_sold;
                    $toExcel['sold_price'] = abs($sold_price);
                    $toExcel['net_sales'] = $qty_sold * $sold_price;
                    if (substr($itemNumber, 0, 3) !== '100') {
                        $toExcel['rr_ref'] = ($toExcel['net_sales'] == 0 || $toExcel['net_sales'] == '') ? 'GWP' : $itemNumber;
                    }else{
                        $toExcel['rr_ref'] = $itemNumber;
                    }
                    $toExcel['store_cost'] = $itemDetails[$itemNumber]['store_cost'];
                    if($masterfile->channel_id == 7 || $masterfile->channel_id == 10){
                        $toExcel['store_cost_eccom'] =  0;
                    }else{
                        $toExcel['store_cost_eccom'] =  $itemDetails[$itemNumber]['store_cost_eccom'];
                    }
                    $toExcel['landed_cost'] = $itemDetails[$itemNumber]['landed_cost'];
                    $toExcel['sales_memo_ref'] = $excel['PromotionID_32'] ?? $excel['PromotionID_35'];
                    $toExcel['item_serial'] = $item_serial;
                    $toExcel['sales_person'] = $excel['SALES_PERSON'];
                    $toExcel['pos_transaction_type'] = $excel['Tran_Type'];
                    
                    $toExcelContent[] = $toExcel;
                    // Increment the counter for the next iteration
                    Counter::where('id', 1)->increment('reference_code');
                }
            }
            if (!empty($toExcelContent)) {
                // Create the Excel file using Laravel Excel (Maatwebsite Excel package)
                Excel::store(new StoreSalesExcel($toExcelContent), $excel_path, 'local');
        
                // Full path of the stored Excel file
                $full_excel_path = storage_path('app') . '/' . $excel_path;
        
                // Prepare arguments for the job
                $args = [
                    'batch_number' => $batch_number,
                    'excel_path' => $full_excel_path,
                    'report_type' => $this->report_type,
                    'folder_name' => $folder_name,
                    'file_name' => $excel_file_name,
                    'created_by' => CRUDBooster::myId() ?? 136,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'data_type' => 'PULL'
                ];
        
                // Dispatch the processing job for each store
                ProcessStoreSalesUploadJob::dispatch($args);
            } else {
                // Log or handle the case when there is no data to export
                Log::info("No new data for store ID: $storeId. Skipping job dispatch.");
            }
        }
    }
    
    function prepareItemData($item, $orgName, $ecomStoreMargin = 0) {
        return [
            'org' => $orgName,
            'item_description' => $item->item_description,
            'store_cost' => $item->dtp_rf ?? 0,
            'store_cost_eccom' => $ecomStoreMargin ?? 0,
            'landed_cost' => $item->landed_cost ?? 0,
            'inventory_type_id' => $item->inventory_types_id ?? NULL,
            'rr_ref' => $item->current_srp == 0 ? 'GWP' : $item->digits_code
        ];
    }
    
    function fetchItemDataInBatch($itemNumbers)
    {
        $results = [];
        $itemNumberSet = collect($itemNumbers)->unique();
        
        // Check in item_masters
        $itemMasterRecords = DB::connection('imfs')
        ->table('item_masters') 
        ->whereIn('digits_code', $itemNumberSet)
        ->get();

        foreach ($itemMasterRecords as $record) {
            $results[$record->digits_code] = self::prepareItemData($record, 'DIGITS', $record->ecom_store_margin);
        }

        $foundItemNumbers = collect(array_keys($results));
        $missingItemNumbers = $itemNumberSet->diff($foundItemNumbers); 

        if ($missingItemNumbers->isEmpty()) {
            return $results;
        }

        //Acounting item master
        $accountingItemMasterRecords = DB::connection('imfs')
        ->table('accounting_items') 
        ->whereIn('digits_code', $itemNumberSet)
        ->get();

        foreach ($accountingItemMasterRecords as $record) {
            $results[$record->digits_code] = self::prepareItemData($record, 'DIGITS');
        }

        $foundItemNumbersAccounting = collect(array_keys($results));
        $missingItemNumbersAccounting = $itemNumberSet->diff($foundItemNumbersAccounting); 

        if ($missingItemNumbersAccounting->isEmpty()) {
            return $results;
        }

        // Check in rma_item_masters
        $rmaItemMasterRecords = DB::connection('imfs')
        ->table('rma_item_masters')
        ->whereIn('digits_code', $missingItemNumbers)
        ->get();

        foreach ($rmaItemMasterRecords as $record) {
            $results[$record->digits_code] = self::prepareItemData($record, 'RMA');
        }

        // Check for missing item numbers again
        $foundItemNumbersAfterRMA = collect(array_keys($results));
        $missingItemNumbersAfterRMA = $missingItemNumbersAccounting->diff($foundItemNumbersAfterRMA);

        if ($missingItemNumbersAfterRMA->isNotEmpty()) {
            // Check in aimfs database
            $aimfsItemMasterRecords = DB::connection('aimfs')
                ->table('digits_imfs')
                ->whereIn('digits_code', $missingItemNumbersAfterRMA)
                ->get();

            foreach ($aimfsItemMasterRecords as $record) {
                $results[$record->digits_code] = self::prepareItemData($record, 'ADMIN');
            }
        }

        return $results;
    }
    
}