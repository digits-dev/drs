<?php

namespace App\Http\Controllers;

use App\Models\SUpplierIntransitInventory;
use App\Models\SUpplierIntransitInventoryInventoriesReport;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\SUpplierIntransitInventoryExport;
use App\Imports\SUpplierIntransitInventoryImport;
use App\Jobs\ProcessSUpplierIntransitInventoryUploadJob;
use App\Models\SUpplierIntransitInventoryUpload;
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
use App\Jobs\ExportSUpplierIntransitInventoryCreateFileJob;
use App\Jobs\AppendMoreSUpplierIntransitInventoryJob;
use Illuminate\Support\Facades\Storage;
use App\Models\ReportPrivilege;
use Illuminate\Support\Facades\Response;
use Rap2hpoutre\FastExcel\FastExcel;

class SupplierIntransitInventoryController extends Controller
{
    private $report_type;

    public function __construct(){
        $this->report_type = ['SUPPLIER INTRANSIT INVENTORY'];
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function supplierIntransitInventoryUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Supplier Intransit Inventory';
        $data['uploadRoute'] = route('supplier-intransit-inventory.upload');
        $data['uploadTemplate'] = route('supplier-intransit-inventory.template');
        $data['nextSeries'] = SupplierIntransitInventory::getNextReference();
        return view('inventory.upload',$data);
    }

    public function supplierIntransitInventoryUpload(Request $request)
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
            ->storeAs("supplier-intansit-inventory-upload/$folder_name", $excel_file_name, 'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        //check headings
        $header = config('excel-template-headers.supplier-intransit-inventory');

        for ($i = 0; $i < sizeof($headings); $i++) {
            if (!in_array($headings[$i], $header)) {
                $unMatch[] = $headings[$i];
            }
        }

        $batch_number = $time;

        if(!empty($unMatch)) {
            return redirect(route('supplier-intransit-inventory.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
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

        ProcessSupplierIntransitInventoryUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {

        $batch_details = Bus::findBatch($batch_id);
        $upload_details = SupplierIntransitInventoryUpload::where('job_batches_id', $batch_id)->first();
        $count = SupplierIntransitInventory::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.supplier-intransit-inventory');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'supplier-intransit-inventory-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportSupplierIntransitInventory(Request $request) {
        $filename = $request->input('filename').'.tsv';
        $filters = $request->all();
        $userReport = ReportPrivilege::myReport(9,CRUDBooster::myPrivilegeId());
        $query = SupplierIntransitInventory::filterForReport(SupplierIntransitInventory::generateReport(), $filters)
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
}
