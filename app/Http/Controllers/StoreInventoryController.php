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
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

    public function exportInventory(Request $request) {
        $filename = $request->input('filename');
        return Excel::download(new StoreInventoryExport, $filename.'.xlsx');
    }

    public function filterStoreInventory(Request $request) {
        $data['result'] = StoreInventoriesReport::searchFilter($request->all())->paginate(10);
        $data['result']->appends($request->except(['_token']));

        $data['channel_name'] = $request->channel_name;
        $data['datefrom'] = $request->datefrom;
        $data['dateto'] = $request->dateto;
        $data['system_name'] = $request->system_name;
        
        return view('store-inventory.filtered-report',$data);

    }
}