<?php

namespace App\Http\Controllers;

use App\Models\StoreSale;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreSalesExport;
use App\Imports\StoreSalesImport;
use App\Jobs\StoreSalesImportJob;
use App\Models\ReportType;
use App\Models\StoreSalesUpload;
use App\Models\StoreSalesUploadLine;
use App\Rules\ExcelFileValidationRule;
use Carbon\Carbon;
use crocodicstudio\crudbooster\helpers\CRUDBooster as HelpersCRUDBooster;
use CRUDBooster;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class StoreSaleController extends Controller
{
    private $reportType;

    public function __construct(){
        $this->reportType = ['STORE SALES'];
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
        $errors = array();
        $request->validate([
            'import_file' => ['required', 'file', new ExcelFileValidationRule(20)],
        ]);
        $time = time();
        $folder_name = "$time-" . Str::random(5);
        $folder_path = storage_path('app') . '/' . $folder_name;
        $excel_file_name = $request->import_file->getClientOriginalName();
        $excel_relative_path = $request->file('import_file')
            ->storeAs("sales-upload/$folder_name", $excel_file_name, 'local');

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

        $batchNumber = $time;
        // $reportType = $request->report_type;

        if(!empty($unMatch)) {
            return redirect(route('store-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
        }
        HeadingRowFormatter::default('slug');
        $excel_data = Excel::toArray(new StoreSalesImport($batchNumber), $excel_path)[0];
        $snaked_headings = array_keys($excel_data[0]);
        $row_count = count($excel_data);
        $chunk_count = 10;
        $chunks = array_chunk($excel_data, $chunk_count);

        $store_sales_upload = new StoreSalesUpload([
            'batch' => $time,
            'folder_name' => $folder_name,
            'file_name' => $excel_file_name,
            'row_count' => $row_count,
            'chunk_count' => $chunk_count,
            'headings' => json_encode($snaked_headings),
            'created_by' => CRUDBooster::myId(),
        ]);

        $store_sales_upload->save();
        
        foreach ($chunks as $key => $chunk) {
            $json = json_encode($chunk);
            $store_sales_upload_line = StoreSalesUploadLine::create([
                'store_sales_uploads_id' => $store_sales_upload->id,
                'chunk_index' => $key,
                'chunk_data' => $json,
            ]);
        }

        dd('doneee :))');


        $excelReportType = array_unique(array_column($excel_data[0], "report_type"));
        foreach ($excelReportType as $keyReportType => $valueReportType) {
            if(!in_array($valueReportType,$this->reportType)){
                array_push($errors, 'report type "'.$valueReportType.'" mismatched!');
            }
        }

        if(!empty($errors)){
            File::delete($excel_path);
            return redirect()->back()->withErrors(['msg' => $errors]);
        }

        ini_set('memory_limit',-1);
        $storeSales = new StoreSalesImport($batchNumber);
        $storeSales->import($excel_path);

        if($storeSales->failures()->isNotEmpty()){
            return back()->withFailures($storeSales->failures());
        }

        // StoreSalesImportJob::dispatch($excel_path);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.store-sales');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'store-sales-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportSales(Request $request) {
        $filename = $request->input('filename');
        return Excel::download(new StoreSalesExport, $filename.'.xlsx');
    }
}
