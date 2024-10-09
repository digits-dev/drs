<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DB;
use CRUDBooster;
use Excel;
use App\Imports\UpdateItems;

class RequestController extends Controller
{

    public function updateSaveItem(Request $request) {
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        $headings = array_filter((new HeadingRowImport)->toArray($path)[0][0]);
        $table_name = $request->table_name;
        try {
            Excel::import(new UpdateItems($table_name),$path);
            CRUDBooster::redirect(CRUDBooster::adminpath($table_name), trans("Update Successfully!"), 'success');
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $error = [];
            foreach ($failures as $failure) {
                $line = $failure->row();
                foreach ($failure->errors() as $err) {
                    $error[] = $err . " on line: " . $line; 
                }
            }
            
            $errors = collect($error)->unique()->toArray();
    
        }
        CRUDBooster::redirect(CRUDBooster::adminpath($table_name), $errors[0], 'danger');
        
    }

    public function showChangePassword(){
		$data['page_title'] = 'Change Password';
		return view('user-account.change-password',$data);
	}
}
