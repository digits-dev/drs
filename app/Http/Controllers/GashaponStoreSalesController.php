<?php

namespace App\Http\Controllers;

use App\Models\GashaponStoreSales;
use App\Models\GashaponStoreSalesReport;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\GashaponStoreSalesExport;
use App\Exports\StoreTestExportBatches;
use App\Imports\GashaponStoreSalesImport;
use App\Jobs\ProcessGashaponStoreSalesUploadJob;
use App\Jobs\GashaponStoreSalesImportJob;
use App\Models\ReportType;
use App\Models\GashaponStoreSalesUpload;
use App\Models\GashaponStoreSalesUploadLine;
use App\Rules\ExcelFileValidationRule;
use App\Jobs\ExportGashaponStoreSalesCreateFileJob;
use App\Jobs\AppendMoreGashaponStoreSalesJob;
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

class GashaponStoreSalesController extends Controller
{
    private $report_type;
    public $batchId;
    private $userReport;
    public function __construct(){
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping("enum", "string");
        $this->report_type = ['GASHAPON STORE SALES'];
        $this->userReport = ReportPrivilege::myReport(8,CRUDBooster::myPrivilegeId());
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

    public function storeSalesUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Gashapon Store Sales';
        $data['uploadRoute'] = route('gashapon-store-sales.upload');
        $data['uploadTemplate'] = route('gashapon-store-sales.template');
        $data['nextSeries'] = GashaponStoreSales::getNextReference();
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
            ->storeAs("gashapon-store-sales-upload/$folder_name", $excel_file_name, 'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        //check headings
        $header = config('excel-template-headers.gashapon-store-sales');
 
        for ($i = 0; $i < sizeof($headings); $i++) {
            if (!in_array($headings[$i], $header)) {
                $unMatch[] = $headings[$i];
            }
        }

        $batch_number = $time;

        if (!empty($unMatch)) {
            return redirect(route('gashapon-store-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
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

        ProcessGashaponStoreSalesUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {

        $batch_details = Bus::findBatch($batch_id);
        $upload_details = GashaponStoreSalesUpload::where('job_batches_id', $batch_id)->first();
        $count = GashaponStoreSales::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.gashapon-store-sales');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'gashapon-store-sales-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportSales(Request $request) {
        $filename = $request->input('filename').'.tsv';
        $filters = $request->all();
        $userReport = ReportPrivilege::myReport(8,CRUDBooster::myPrivilegeId());
        $query = GashaponStoreSales::filterForReport(GashaponStoreSales::generateReport(), $filters)
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
}


