<?php

namespace App\Http\Controllers;

use App\Models\StoreSale;
use App\Models\StoreSalesReport;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreSalesExport;
use App\Imports\StoreSalesImport;
use App\Jobs\ProcessStoreSalesUploadJob;
use App\Jobs\StoreSalesImportJob;
use App\Models\ReportType;
use App\Models\StoreSalesUpload;
use App\Models\StoreSalesUploadLine;
use App\Rules\ExcelFileValidationRule;
use Carbon\Carbon;
use CRUDBooster;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class StoreSaleController extends Controller
{
    private $report_type;

    public function __construct(){
        $this->report_type = ['STORE SALES'];
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
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 36000);
        $filename = $request->input('filename');
        $filters = $request->all();
        $query = StoreSale::filterForReport(StoreSale::generateReport(), $filters)
            ->where('is_final', 1);
        return Excel::download(new StoreSalesExport($query), $filename.'.xlsx');
    }
}