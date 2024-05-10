<?php

namespace App\Http\Controllers;

use App\Models\StoreInventory;
use App\Models\StoreInventoriesReport;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreInventoryExport;
use App\Imports\StoreInventoryImport;
use App\Jobs\ProcessStoreInventoryUploadJob;
use App\Models\StoreInventoryUpload;
use App\Rules\ExcelFileValidationRule;
use CRUDBooster;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use DB;
use App\Jobs\ExportStoreInventoryCreateFileJob;
use App\Jobs\AppendMoreStoreInventoryJob;
use Illuminate\Support\Facades\Storage;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Response;

class StoreInventoryController extends Controller
{
    private $report_type;

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
        $userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
    
        $headers = [
            'Content-Type' => 'text/tab-separated-values',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
  
        // Open a stream to write the response body
        $callback = function () use($userReport, $filters) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($handle, explode(",",$userReport->report_header), "\t"); // Specify column names

            // Query and stream data
            StoreInventory::filterForReport(StoreInventory::generateReport(), $filters)
            ->where('is_final', 1)
            ->chunk(5000, function ($data) use ($handle, $userReport) {
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

}