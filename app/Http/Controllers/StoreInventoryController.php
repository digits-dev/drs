<?php

namespace App\Http\Controllers;

use App\Models\StoreInventory;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\StoreInventoryExport;
use App\Imports\StoreInventoryImport;
use App\Models\ReportType;
use App\Rules\ExcelFileValidationRule;
use Carbon\Carbon;
use CRUDBooster;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;

class StoreInventoryController extends Controller
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
        $header = config('excel-template-headers.store-inventory');

        for ($i=0; $i < sizeof($headings[0][0]); $i++) {
            if (!in_array($headings[0][0][$i], $header)) {
                $unMatch[] = $headings[0][0][$i];
            }
        }

        $batchNumber = time();
        // $reportType = $request->report_type;

        if(!empty($unMatch)) {
            return redirect(route('store-inventory.upload-view'))->with(['message_type' => 'danger', 'message' => trans("crudbooster.aler_mismatched_headers")]);
        }
        HeadingRowFormatter::default('slug');

        // if(!empty($errors)){
        //     return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
        // }
        ini_set('memory_limit',-1);
        $storeInventory = new StoreInventoryImport($batchNumber);
        $storeInventory->import($path);

        if($storeInventory->failures()->isNotEmpty()){
            return back()->withFailures($storeInventory->failures());
        }

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function uploadTemplate()
    {
        $header = config('excel-template-headers.store-inventory');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'store-inventory-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportInventory(Request $request) {
        $filename = $request->input('filename');
        return Excel::download(new StoreInventoryExport, $filename.'.xlsx');
    }
}
