<?php

namespace App\Http\Controllers;

use App\Models\StoreSale;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreSalesExport;
use App\Imports\StoreSalesImport;
use App\Models\ReportType;
use App\Rules\ExcelFileValidationRule;
use Carbon\Carbon;
use CRUDBooster;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;

class StoreSaleController extends Controller
{
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
        $path_excel = $request->file('import_file')
            ->storeAs('temp',$request->import_file->getClientOriginalName(),'local');

        $path = storage_path('app').'/'.$path_excel;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($path);
        //check headings
        $header = config('excel-template-headers.store-sales');

        for ($i=0; $i < sizeof($headings[0][0]); $i++) {
            if (!in_array($headings[0][0][$i], $header)) {
                $unMatch[] = $headings[0][0][$i];
            }
        }

        $batchNumber = time();
        // $reportType = $request->report_type;

        if(!empty($unMatch)) {
            return redirect(route('store-sales.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.aler_mismatched_headers")]);
        }
        HeadingRowFormatter::default('slug');

        // if(!empty($errors)){
        //     return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
        // }
        ini_set('memory_limit',-1);
        $storeSales = new StoreSalesImport($batchNumber);
        $storeSales->import($path);

        if($storeSales->failures()->isNotEmpty()){
            return back()->withFailures($storeSales->failures());
        }

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!']);
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
