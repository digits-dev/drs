<?php

namespace App\Http\Controllers;

use App\Exports\DigitsSalesExport;
use App\Exports\ExcelTemplate;
use App\Imports\DigitsSalesImport;
use App\Jobs\ProcessDigitsSalesUploadJob;
use App\Models\DigitsSale;
use App\Models\DigitsSalesReport;
use App\Models\DigitsSalesUpload;
use Illuminate\Http\Request;
use App\Rules\ExcelFileValidationRule;
use CRUDBooster;
use DateTime;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Str;
use DB;
use App\Jobs\ExportDigitsSalesCreateFileJob;
use App\Jobs\AppendMoreDigitsSalesJob;
use Illuminate\Support\Facades\Storage;

class DigitsSaleController extends Controller
{

    private $reportType;

    public function __construct(){
        $this->report_type = ['DIGITS SALES'];
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
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function show(DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function edit(DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(DigitsSale $digitsSale)
    {
        //
    }

    public function digitsSalesUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Digits Sales';
        $data['uploadRoute'] = route('digits-sales.upload');
        $data['uploadTemplate'] = route('digits-sales.template');
        $data['nextSeries'] = DigitsSale::getNextReference();
        return view('sales.upload',$data);
    }

    public function digitsSalesUpload(Request $request)
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
            ->storeAs("digits-sales-upload/$folder_name", $excel_file_name, 'local');
        $path_excel = $request->file('import_file')
            ->storeAs('temp',$request->import_file->getClientOriginalName(),'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        //check headings
        
        $header = config('excel-template-headers.digits-sales');

        for ($i = 0; $i < sizeof($headings); $i++) {
            if (!in_array($headings[$i], $header)) {
                $unMatch[] = $headings[$i];
            }
        }

        $batch_number = $time;

        if(!empty($unMatch)) {
            return redirect(route('digits-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
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

        ProcessDigitsSalesUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {
        $batch_details = Bus::findBatch($batch_id);
        $upload_details = DigitsSalesUpload::where('job_batches_id', $batch_id)->first();
        $count = DigitsSale::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.digits-sales');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'digits-sales-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportSales(Request $request) {
        $filename = $request->input('filename').'.csv';
        $filters = $request->all();
        // $query = DigitsSale::filterForReport(DigitsSale::generateReport(), $filters);
        $digitsSalesCount = DigitsSale::filterForReport(DigitsSale::generateReport(), $filters)->count();
        if($digitsSalesCount == 0){
            return response()->json(['msg'=>'Nothing to export','status'=>'error']);
        }
        $chunkSize = 5000;
        $numberOfChunks = ceil($digitsSalesCount / $chunkSize);
        $folder = 'digits-sales'.'-'.now()->toDateString() . '-' . str_replace(':', '-', now()->toTimeString()) . '-' . CRUDBooster::myId();
        $batches = [
            new ExportDigitsSalesCreateFileJob($chunkSize, $folder, $filters, $filename)
        ];
     
        if ($storeSalesCount > $chunkSize) {
            $numberOfChunks = $numberOfChunks - 1;
            for ($numberOfChunks; $numberOfChunks > 0; $numberOfChunks--) {
                $batches[] = new AppendMoreDigitsSalesJob($numberOfChunks, $chunkSize, $folder, $filters, $filename);
            }
        }

        $batch = Bus::batch($batches)
            ->name('Export Digits Sales')
            ->then(function (Batch $batch) use ($folder) {
                $path = "exports/{$folder}/ExportDigitsSales.csv";
                // upload file to s3
                $file = storage_path("app/{$folder}/ExportDigitsSales.csv");
                Storage::disk('s3')->put($path, file_get_contents($file));
                // send email to admin
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // send email to admin or log error
            })
            ->finally(function (Batch $batch) use ($folder) {
                // delete local file
                // Storage::disk('local')->deleteDirectory($folder);
            })
            ->dispatch();

        $this->batchId = $batch->id;

        session()->put('lastDigitSalesBatchId',$this->batchId);
        session()->put('folderSalesDigits',$folder);
        // session()->put('filename',$filename);

        return [
            'batch_id' => $this->batchId,
            'folder' => $folder,
            'status'   => 'success',
            'msg'      => 'Success'
        ];
    }

    public function progressExport(Request $request){
        try{
            $batchId = $request->batchId ?? session()->get('lastDigitSalesBatchId');
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