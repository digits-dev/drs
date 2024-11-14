<?php namespace App\Http\Controllers;

	use Illuminate\Http\Request;
	use Session;
	use DB;
	use CRUDBooster;
	use App\Exports\BreakevenTemplateExport;
	use Maatwebsite\Excel\Facades\Excel;
	use App\Imports\BreakevenImport;
	use App\Models\Customer;
	use Illuminate\Support\Str;

	class AdminBreakevenSalesController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->table = "breakeven_sales";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Stores Id","name"=>"stores_id", 'join'=>'customers,customer_name'];
			$this->col[] = ["label"=>"Year","name"=>"year"];
			$this->col[] = ["label"=>"Month","name"=>"month"];
			$this->col[] = ["label"=>"Breakeven","name"=>"breakeven"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Stores Id','name'=>'stores_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'stores,id'];
			$this->form[] = ['label'=>'Year','name'=>'year','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Month','name'=>'month','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Breakeven','name'=>'breakeven','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			
			$this->index_button = array();
			if(CRUDBooster::getCurrentMethod() == 'getIndex') {
					$this->index_button[] = ["title"=>"Import Items","label"=>"Import Items",'color'=>'info',"icon"=>"fa fa-upload","url"=>CRUDBooster::mainpath('breakeven-import-view')];
					
			};
			
	    }

		public function importBreakevenView() {
			if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {    
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
			}
			$data['page_title'] = 'Breakeven Sales';
			return view('breakeven-sales/breakeven-sales',$data);
		}

		public function importBreakevenTemplate()
		{
			$fileName = 'Breakeven-Template_' . date("Ymd"). '.csv';
			return Excel::download(new BreakevenTemplateExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
		}

		// public function importBreakevenSales(Request $request){
		// 	$cnt_success = 0;
		// 	$file = $request->file('import_file');
			
		// 	$validator = \Validator::make(
		// 		['file' => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
		// 		['file' => 'required', 'extension' => 'required|in:csv',]
		// 	);
			
		// 	if ($validator->fails()) {
		// 		return back()->with('error_import', 'Failed ! Please check required file extension.');
		// 	}
			
		// 	if ($request->hasFile('import_file')) {
		// 		$path = $request->file('import_file')->getRealPath();
		// 		$csv = array_map('str_getcsv', file($path));
		// 		$dataExcel = Excel::toCollection(null, $path);

		// 		$unMatch = [];
		// 		$header = array_map(function ($value) {
		// 		return trim($value, "\xEF\xBB\xBF"); // Remove BOM characters
		// 		}, $csv[0]);
				
		// 		$expectedHeader = [
		// 			'Customer Name', 'Year', 'Month', 'Breakeven Value'
		// 		];
				
		// 		$unMatch = [];
		// 		foreach ($header as $i => $column) {
		// 			if (!in_array($column, $expectedHeader)) {
		// 				$unMatch[] = $column;
		// 			}
		// 		}
				
		// 		  if(!empty($unMatch)) {
		// 			  return back()->with('error_import', 'Failed ! Please check template headers, mismatched detected.');
		// 		  }
		// 		  if(!empty($dataExcel)) {
	  
	  
		// 			  foreach ($dataExcel as $key => $value) {
		// 				  $data = array();
		// 				  $line_item = 0;	
		// 				  dd($value);
		// 				  $line_item = $key+1;
		// 				  $nullItems = array_filter($value, function ($obj) {
		// 					  return $obj == null;
		// 				  });
	  
		// 				  if(!empty($nullItems)){
		// 					  $nullColumns = strtoupper(str_replace('_',' ',array_keys($nullItems)[0]));
		// 					  array_push($this->errors, "Line $line_item : $nullColumns is blank!");
		// 				  }
	  
		// 				  if(!empty($this->errors)){
		// 					  return back()->with('error_import', implode("<br>", $this->errors));
		// 				  }
	  
		// 				  $data = [
		// 					  'stores_id' => $value['customer_name'],
		// 					  'year' => $value['year'],
		// 					  '	month' => $value['month'],
		// 					  'breakeven' => $value['breakeven'],
		// 					  'created_at' => date('Y-m-d H:i:s'),
		// 				  ];
	  
		// 				  try {
		// 					  if(empty($this->errors)){
		// 						  $cnt_success++;
		// 						  GachaItemApproval::updateOrInsert(['jan_no' => $jan_number],$data);
		// 					  }
	  
		// 				  } catch (\Exception $e) {
		// 					  array_push($this->errors, "Line $line_item : with error ".json_encode($e));
		// 				  }
		// 			  }
		// 		  }
		// 	  }
	  
		// 	  if(empty($this->errors)){
		// 		  return back()->with('success_import', "Success ! $cnt_success item(s) were created/updated successfully.🤩🤩🤩");
		// 	  }
		// 	  else{
		// 		  return back()->with('error_import', implode("<br>", $this->errors));
		// 	  }
		//   }

		public function importBreakevenSales(Request $request){

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
			$header = ['Customer Name', 'Year', 'Month', 'Breakeven Value'];
			
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
			
			Excel::import(new BreakevenImport, $request->file('import_file'));
			return back()->with('success_import', "Success ! item(s) were created successfully.");
		}
	}
	   