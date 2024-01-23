<?php

namespace App\Http\Controllers;

use App\Models\WarehouseInventory;
use Illuminate\Http\Request;
use App\Exports\ExcelTemplate;
use App\Exports\WarehouseInventoryExport;
use App\Imports\WarehouseInventoryImport;
use App\Jobs\ProcessWarehouseInventoryUploadJob;
use App\Models\WarehouseInventoryUpload;
use App\Rules\ExcelFileValidationRule;
use CRUDBooster;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class WarehouseInventoryController extends Controller
{
    private $reportType;

    public function __construct(){
        $this->report_type = ['WAREHOUSE INVENTORY','WAREHOUSE INTRANSIT'];
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
     * @param  \App\Models\WarehouseInventory  $warehouseInventory
     * @return \Illuminate\Http\Response
     */
    public function show(WarehouseInventory $warehouseInventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WarehouseInventory  $warehouseInventory
     * @return \Illuminate\Http\Response
     */
    public function edit(WarehouseInventory $warehouseInventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WarehouseInventory  $warehouseInventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WarehouseInventory $warehouseInventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WarehouseInventory  $warehouseInventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(WarehouseInventory $warehouseInventory)
    {
        //
    }

    public function warehouseInventoryUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Warehouse Inventory';
        $data['uploadRoute'] = route('warehouse-inventory.upload');
        $data['uploadTemplate'] = route('warehouse-inventory.template');
        $data['nextSeries'] = WarehouseInventory::getNextReference();
        return view('inventory.upload',$data);
    }

    public function warehouseInventoryUpload(Request $request)
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
            ->storeAs("warehouse-inventory-upload/$folder_name", $excel_file_name, 'local');

        $excel_path = storage_path('app') . '/' . $excel_relative_path;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($excel_path)[0][0];
        //check headings
        $header = config('excel-template-headers.warehouse-inventory');

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

        ProcessWarehouseInventoryUploadJob::dispatch($args);

        return redirect()->back()->with(['message_type' => 'success', 'message' => 'Upload processing!'])->send();
    }

    public function getBatchDetails($batch_id) {

        $batch_details = Bus::findBatch($batch_id);
        $upload_details = WarehouseInventoryUpload::where('job_batches_id', $batch_id)->first();
        $count = WarehouseInventory::where('batch_number', $upload_details->batch)->count();
        return [
            'batch_details' => $batch_details,
            'upload_details' => $upload_details,
            'count' => $count,
        ];
    }


    public function uploadTemplate()
    {
        $header = config('excel-template-headers.warehouse-inventory');
        $export = new ExcelTemplate([$header]);
        return Excel::download($export, 'warehouse-inventory-'.date("Ymd").'-'.date("h.i.sa").'.xlsx');
    }

    public function exportInventory(Request $request) {
        $filename = $request->input('filename');
        return Excel::download(new WarehouseInventoryExport, $filename.'.xlsx');
    }
}
