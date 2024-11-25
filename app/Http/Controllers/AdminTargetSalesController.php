<?php namespace App\Http\Controllers;

		use Illuminate\Http\Request;
		use Session;
		use DB;
		use CRUDBooster;
		use App\Exports\TargetTemplateExport;
		use Maatwebsite\Excel\Facades\Excel;
		use App\Imports\TargetSalesImport;
		use App\Models\Customer;
		use Illuminate\Support\Str;

	class AdminTargetSalesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = false;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "target_sales";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Customer Name","name"=>"stores_id", 'join'=>'customers,customer_name'];
			$this->col[] = ["label"=>"Year","name"=>"year"];
			$this->col[] = ["label"=>"Month","name"=>"month"];
			$this->col[] = ["label"=>"Target Sales","name"=>"target_sales"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Customer Name','name'=>'stores_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'stores,id'];
			$this->form[] = ['label'=>'Year','name'=>'year','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Month','name'=>'month','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Target Sales','name'=>'target_sales','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

	        
			
			$this->index_button = array();
			if(CRUDBooster::getCurrentMethod() == 'getIndex') {
					$this->index_button[] = ["title"=>"Import Items","label"=>"Import Items",'color'=>'info',"icon"=>"fa fa-upload","url"=>CRUDBooster::mainpath('target-import-view')];
					
			};
			
	    }

		public function importTargetSalesView() {
			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}
			$data['page_title'] = 'Target Sales';
			return view('target-sales/target-sales',$data);
		}

		public function importTargetSalesTemplate()
		{
			$fileName = 'Target-Template_' . date("Ymd"). '.csv';
			return Excel::download(new TargetTemplateExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
		}
		
		public function importTargetSales(Request $request){

			$file = $request->file('import_file');
			$errors = [];
			
			if (!$request->hasFile('import_file')) {
				return back()->with('error_import', 'Failed ! Please select a file to import.');
			}

			$validator = \Validator::make(
				['file' => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
				['file' => 'required', 'extension' => 'required|in:csv',]
			);

			if ($validator->fails()) {
				return back()->with('error_import', 'Failed ! Please check required file extension.');
			}

			$path = $file->getRealPath();
			$dataExcel = Excel::toArray(null, $path)[0];  // Get the first sheet
			$csv = array_map('str_getcsv', file($path));
			$unMatch = [];
			$header = ['Customer Name', 'Year', 'Month', 'Target Value'];
			
			for ($i = 0; $i < sizeof($csv[0]); $i++) {
				// Remove BOM (if present) using a regex
				$csvHeaderValue = preg_replace('/^\xEF\xBB\xBF/', '', $csv[0][$i]);
			
				// Make the comparison case-insensitive by converting both to lowercase
				if (!in_array(strtolower($csvHeaderValue), array_map('strtolower', $header))) {
					$unMatch[] = $csvHeaderValue;
				}
			}
			
			if(!empty($unMatch)) {
				return back()->with('error_import', 'Failed ! Please check template headers, mismatched detected.');
			}

			$header = array_map(function ($header) {
				return Str::snake($header);
			}, $dataExcel[0]);

			$dataExcel = array_map(function ($row) use ($header) {
				return array_combine($header, $row); 
			}, array_slice($dataExcel, 1));  
			
	
				if(!empty($dataExcel)) {
					
					foreach ($dataExcel as $key => $value) {
						$line_item = 0;	
						$line_item = $key+1;
						
					$nullItems = array_filter($value, function ($obj) {
						return $obj == null;
					});

					if(!empty($nullItems)){
						$nullColumns = strtoupper(str_replace('_',' ',array_keys($nullItems)[0]));
						array_push($errors, "Line $line_item : $nullColumns is blank!");
					}

					$value['customer_name'] = trim($value['customer_name']);
					$itemExists = Customer::where('customer_name', $value['customer_name'])->first();

					// validate customer name
					if(is_null($itemExists)){
						array_push($errors, "Customer Name {$value['customer_name']} on line $line_item does not exist.");
						// return back()->with('error_import', implode("<br>", $errors));
					}
					// validate year
					if (!isset($value['year']) || !preg_match('/^(19|20)\d{2}$/', $value['year'])) {
						array_push($errors, "Invalid year format for {$value['year']} on line $line_item. Please provide a valid 4-digit year.");
					}
					// validate month
				    if (!isset($value['month']) || !preg_match('/^(0?[1-9]|1[0-2])$/', $value['month'])) {
						array_push($errors, "Invalid month format for {$value['month']} on line $line_item. Please provide a valid month (1 to 12).");
					}
				
				}
			}
			if (!empty($errors)) {
				// If there are errors, redirect back with error messages
				return back()->with('error_import', implode('<br>', $errors));
			}
			
			Excel::import(new TargetSalesImport, $request->file('import_file'));
			return back()->with('success_import', "Success ! item(s) were created successfully.");
		}
	}