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

class StoreSaleController extends Controller
{
    private $report_type;
    public $batchId;
    private $userReport;
    public function __construct(){
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
        $this->report_type = ['STORE SALES'];
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
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
    public function StoresSalesFromPosEtp(){
        $result = StoreSale::getStoresSalesFromPosEtp();
        // Group sales data by store ID
        $groupedSalesData = collect($result)->groupBy('STORE ID');

        foreach ($groupedSalesData as $storeId => $storeData) {
            $time = microtime(true);
            $batch_number = str_replace('.', '', $time);;
            $folder_name = "$batch_number-" . Str::random(5);
            $dateNow = Carbon::now()->format('Ymd');
            $excel_file_name = "stores-sales-$dateNow.xlsx";
            $excel_path = "store-sales-upload/$folder_name/$excel_file_name";
    
            if (!file_exists(storage_path("app/store-sales-upload/$folder_name"))) {
                mkdir(storage_path("app/store-sales-upload/$folder_name"), 0777, true);
            }
            $toExcelContent = [];
            // Initialize the cache arrays
            $itemMasterCache = [];
            $rmaItemMasterCache = [];
            $aimfsItemMasterCache = [];
            $masterfileCache = [];
            // Create Excel Data

            foreach ($storeData as &$excel) {
                $counter = Counter::where('id', 1)->value('reference_code');
                $modified = [];
                foreach ($excel as $key => $value) {
                    // Replace spaces with underscores in keys
                    $newKey = str_replace(' ', '_', $key);
                    $modified[$newKey] = $value;
                }
                $excel = $modified;
            
                // ITEM MASTERS CACHING
                $itemNumber = $excel['ITEM_NUMBER'];
                $item_master = $itemMasterCache[$itemNumber] ??= DB::connection('imfs')->table('item_masters')->where('digits_code', $itemNumber)->first();
            
                // RMA ITEM MASTERS CACHING
                $rma_item_master = $rmaItemMasterCache[$itemNumber] ??= DB::connection('imfs')->table('rma_item_masters')->where('digits_code', $itemNumber)->first();
            
                // ADMIN ITEM MASTERS CACHING
                $aimfs_item_master = $aimfsItemMasterCache[$itemNumber] ??= DB::connection('aimfs')->table('digits_imfs')->where('digits_code', $itemNumber)->first();
            
                // Determine the organization and set related values
                if ($item_master) {
                    $org = 'DIGITS';
                    $rr_ref = $item_master->current_srp ? $item_master->digits_code : 'GWP';
                    $item_description = $item_master->item_description;
                    $store_cost = $item_master->dtp_rf;
                    $store_cost_eccom = $item_master->ecom_store_margin;
                    $landed_cost = $item_master->landed_cost;
                    $sales_memo_ref = 'RTL';
                } elseif ($rma_item_master) {
                    $org = 'RMA';
                    $rr_ref = $rma_item_master->current_srp ? $rma_item_master->digits_code : 'GWP';
                    $item_description = $rma_item_master->item_description;
                    $store_cost = $rma_item_master->dtp_rf;
                    $store_cost_eccom = 0;
                    $landed_cost = $rma_item_master->landed_cost;
                    $sales_memo_ref = 'RTL';
                } elseif ($aimfs_item_master) {
                    $org = 'ADMIN';
                    $rr_ref = $aimfs_item_master->current_srp ? $aimfs_item_master->digits_code : 'GWP';
                    $item_description = $aimfs_item_master->item_description;
                    $store_cost = $aimfs_item_master->dtp_rf;
                    $store_cost_eccom = 0;
                    $landed_cost = $aimfs_item_master->landed_cost;
                    $sales_memo_ref = 'RTL';
                }
            
                // MASTERFILE CACHING
                $cusCode = "CUS-" . $excel['STORE_ID'];
                $masterfile = $masterfileCache[$cusCode] ??= DB::connection('masterfile')->table('customer')->where('customer_code', $cusCode)->first();
            
                // Prepare data for output
                $toExcel = [];
                $toExcel['reference_number'] = $counter;
                $toExcel['system'] = 'POS';
                $toExcel['org'] = $org;
                $toExcel['report_type'] = 'STORE SALES';
                $toExcel['channel_code'] = $masterfile->channel_code_id;
                $toExcel['customer_location'] = $masterfile->customer_name;
                $toExcel['receipt_number'] = $excel['RECEIPT_#'];
                $toExcel['sold_date'] = Carbon::createFromFormat('Ymd', $excel['SOLD_DATE'])->format('Y-m-d');
                $toExcel['item_number'] = $excel['ITEM_NUMBER'];
                $toExcel['rr_ref'] = $rr_ref;
                $toExcel['item_description'] = $item_description;
                $toExcel['qty_sold'] = $excel['QTY_SOLD'];
                $toExcel['sold_price'] = $excel['SOLD_PRICE'];
                $toExcel['net_sales'] = $excel['NET_SALES'];
                $toExcel['store_cost'] = $store_cost;
                $toExcel['store_cost_eccom'] = $store_cost_eccom;
                $toExcel['landed_cost'] = $landed_cost;
                $toExcel['sales_memo_ref'] = $sales_memo_ref;
                $toExcelContent[] = $toExcel;
            
                // Increment the counter for the next iteration
                Counter::where('id', 1)->increment('reference_code');
            }
            
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
                'created_by' => CRUDBooster::myId(),
                'from_date' => $from_date,
                'to_date' => $to_date,
                'data_type' => 'PULL'
            ];

            // Dispatch the processing job for each store
            ProcessStoreSalesUploadJob::dispatch($args);
        }
    }
}