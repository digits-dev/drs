<?php

namespace App\Http\Controllers;

use App\Exports\DigitsSalesExport;
use App\Exports\ExcelTemplate;
use App\Imports\DigitsSalesImport;
use App\Models\DigitsSale;
use Illuminate\Http\Request;
use App\Rules\ExcelFileValidationRule;
use CRUDBooster;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class DigitsSaleController extends Controller
{

    private $reportType;

    public function __construct(){
        $this->reportType = ['DIGITS SALES'];
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
        $errors = array();
        $request->validate([
            'import_file' => ['required', 'file', new ExcelFileValidationRule(20)],
        ]);
        $path_excel = $request->file('import_file')
            ->storeAs('temp',$request->import_file->getClientOriginalName(),'local');

        $path = storage_path('app').'/'.$path_excel;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport())->toArray($path);
        //check headings
        $header = config('excel-template-headers.digits-sales');

        for ($i=0; $i < sizeof($headings[0][0]); $i++) {
            if (!in_array($headings[0][0][$i], $header)) {
                $unMatch[] = $headings[0][0][$i];
            }
        }

        $batchNumber = time();
        // $reportType = $request->report_type;

        if(!empty($unMatch)) {
            return redirect(route('digits-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.alert_mismatched_headers")]);
        }
        HeadingRowFormatter::default('slug');
        $excelData = Excel::toArray(new DigitsSalesImport($batchNumber), $path);

        $excelReportType = array_unique(array_column($excelData[0], "report_type"));
        foreach ($excelReportType as $keyReportType => $valueReportType) {
            if(!in_array($valueReportType,$this->reportType)){
                array_push($errors, 'report type "'.$valueReportType.'" mismatched!');
            }
        }

        if(!empty($errors)){
            File::delete($path);
            return redirect()->back()->withErrors(['msg' => $errors]);
        }

        ini_set('memory_limit',-1);
        $digitsSales = new DigitsSalesImport($batchNumber);
        $digitsSales->import($path);

        if($digitsSales->failures()->isNotEmpty()){
            return back()->withFailures($digitsSales->failures());
        }

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.digits-sales');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'digits-sales-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportSales(Request $request) {
        $filename = $request->input('filename');
        return Excel::download(new DigitsSalesExport, $filename.'.xlsx');
    }
}
