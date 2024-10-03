<?php namespace App\Http\Controllers;

    use App\Models\Channel;
    use App\Models\System;
    use App\Models\WarehouseInventoriesReport;
    use App\Models\WarehouseInventory;
	use App\Models\ReportPrivilege;
	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use File;
	use Yajra\DataTables\DataTables;

	class AdminWarehouseInventoriesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "reference_number";
			$this->limit = "20";
			$this->orderby = "reference_number,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "warehouse_inventories";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Batch Date","name"=>"batch_date"];
			$this->col[] = ["label"=>"Batch Number","name"=>"batch_number"];
			$this->col[] = ["label"=>"Reference Number","name"=>"reference_number"];
            $this->col[] = ["label"=>"System","name"=>"systems_id","join"=>"systems,system_name"];
            $this->col[] = ["label"=>"Org","name"=>"organizations_id","join"=>"organizations,organization_name"];
            $this->col[] = ["label"=>"Report Type","name"=>"report_types_id","join"=>"report_types,report_type"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];

			/*
	        | ----------------------------------------------------------------------
	        | Sub Module
	        | ----------------------------------------------------------------------
			| @label          = Label of action
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        |
	        */
	        $this->sub_module = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        |
	        */
	        $this->addaction = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Button Selected
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button
	        | Then about the action, you should code at actionButtonSelected method
	        |
	        */
	        $this->button_selected = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------
	        | @message = Text of message
	        | @type    = warning,success,danger,info
	        |
	        */
	        $this->alert = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add more button to header button
	        | ----------------------------------------------------------------------
	        | @label = Name of button
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        |
	        */
	        $this->index_button = array();
            if(CRUDBooster::getCurrentMethod() == 'getIndex') {
                    $this->index_button[] = [
                        "title"=>"Upload Inventory",
                        "label"=>"Upload Inventory",
                        "icon"=>"fa fa-upload",
                        "color"=>"success",
                        "url"=>route('warehouse-inventory.upload-view')];
				$this->index_button[] = ['label'=>'Export Order','url'=>"javascript:showInventoryExport()",'icon'=>'fa fa-download'];
            }


	        /*
	        | ----------------------------------------------------------------------
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
	        |
	        */
	        $this->table_row_color = array();


	        /*
	        | ----------------------------------------------------------------------
	        | You may use this bellow array to add statistic at dashboard
	        | ----------------------------------------------------------------------
	        | @label, @count, @icon, @color
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add javascript at body
	        | ----------------------------------------------------------------------
	        | javascript code in the variable
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code before index table
	        | ----------------------------------------------------------------------
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code after index table
	        | ----------------------------------------------------------------------
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include Javascript File
	        | ----------------------------------------------------------------------
	        | URL of your javascript each array
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add css style at body
	        | ----------------------------------------------------------------------
	        | css code in the variable
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;



	        /*
	        | ----------------------------------------------------------------------
	        | Include css File
	        | ----------------------------------------------------------------------
	        | URL of your css each array
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();


	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for button selected
	    | ----------------------------------------------------------------------
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here

	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate query of index result
	    | ----------------------------------------------------------------------
	    | @query = current sql query
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate row of index table html
	    | ----------------------------------------------------------------------
	    |
	    */
	    public function hook_row_index($column_index,&$column_value) {
	    	//Your code here
	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before add data is execute
	    | ----------------------------------------------------------------------
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after add public static function called
	    | ----------------------------------------------------------------------
	    | @id = last insert id
	    |
	    */
	    public function hook_after_add($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before update data is execute
	    | ----------------------------------------------------------------------
	    | @postdata = input post data
	    | @id       = current id
	    |
	    */
	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_edit($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }

        // public function getIndex() {

        //     if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        //     $data = [];
        //     $data['page_title'] = 'Warehouse Inventory';
        //     $data['channels'] = Channel::active();
        //     $data['systems'] = System::active();
        //     $data['result'] = DB::table('warehouse_inventories_report')
        //         ->limit(50)
        //         ->get();

        //     return view('warehouse-inventory.report',$data);
        // }
		
		public function getIndex() {

            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

            $data = [];
            $data['page_title'] = 'Warehouse Inventory';
            $data['channels'] = Channel::active();
            $data['systems'] = System::active();
     
			
			// $data['result'] = WarehouseInventory::where('is_final', 1)->paginate(10);
			// $ids = $data['result']->pluck('id')->toArray();
			// $data['rows'] = WarehouseInventory::generateReport($ids)->get();

			return view('warehouse-inventory.report-yajra',$data);
        }

		public function getDetail($id) {
			if (!CRUDBooster::isRead()) {
				return CRUDBooster::redirect(CRUDBooster::mainPath(), trans('crudbooster.denied_access'));
			}
			$data = [];
			$data['page_title'] = 'Warehouse Inventory Details';
			$data['warehouse_inventory_details'] = WarehouseInventory::generateReport([$id])->first();
			$data['report_privilege'] = ReportPrivilege::myReport(4,CRUDBooster::myPrivilegeId());

			$headerArray = explode(',', $data['report_privilege']->report_header);
			$queryArray = explode(',', $data['report_privilege']->report_query);
			foreach ($queryArray as &$query) {
				$query = str_replace('`', '', $query);
			}
			
			$data['report'] = array_combine($queryArray, $headerArray);
			
			return view('warehouse-inventory.details',$data);
		}

		// public function filterWarehouseInventory(Request $request) {

        
		// 	$data['searchval'] = $request->search;
		// 	$data['channels_id'] = $request->channels_id;
		// 	$data['datefrom'] = $request->datefrom;
		// 	$data['dateto'] = $request->dateto;
		// 	$data['systems_id'] = $request->systems_id;
	
		// 	$data['result'] = WarehouseInventory::filterForReport(WarehouseInventory::generateReport(), $request->all())
		// 	->where('is_final', 1)
		// 	->paginate(10);
		// 	$data['result']->appends($request->except(['_token']));
		// 	return view('warehouse-inventory.filtered-report',$data);

		// }

		public function filterWarehouseInventory(Request $request) {
			ini_set('memory_limit', '-1');
        	ini_set('max_execution_time', 3000);
			$data['searchval'] = $request->search;
			$data['receipt_number'] = $request->receipt_number;
			$data['channels_id'] = $request->channels_id;
			$data['datefrom'] = $request->datefrom;
			$data['dateto'] = $request->dateto;
			$data['concepts_id'] = $request->concepts_id;
			if($request->datefrom && $request->dateto){
				$query = WarehouseInventory::filterForReport(WarehouseInventory::generateReport(), $request->all())
				->where('is_final', 1);
				$dt = new DataTables();
				return $dt->eloquent($query)
				->filterColumn('systems.system_name', function($query, $keyword) {
					$query->whereRaw("systems.system_name LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('organizations.organization_name', function($query, $keyword) {
					$query->whereRaw("organizations.organization_name LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('report_types.report_type', function($query, $keyword) {
					$query->whereRaw("report_types.report_type LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('channels.channel_code', function($query, $keyword) {
					$query->whereRaw("channels.channel_code LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('customers.customer_name', function($query, $keyword) {
					$query->whereRaw("customers.customer_name LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('employees.employee_name', function($query, $keyword) {
					$query->whereRaw("employees.employee_name LIKE ?", ["%{$keyword}%"]);
				})
				->filterColumn('concepts.concept_name', function($query, $keyword) {
					$query->whereRaw("concepts.concept_name LIKE ?", ["%{$keyword}%"]);
				})
				->addIndexColumn()
				->addColumn('action', function($row){
					$actionBtn = '<a class="btn-detail" title="Detail" href="'.CRUDBooster::adminpath("warehouse_inventories/detail/".$row["id"]).'"><i class="fa fa-eye"></i></a>';
					return $actionBtn;
				})
				->rawColumns(['action'])
				->toJson();
			}else{
				$query = WarehouseInventory::getYajraDefaultData()->where('is_final', 1);
				$dt = new DataTables();
				return $dt->collection($query)
				
				->addIndexColumn()
				->addColumn('action', function($row){
					$actionBtn = '<a class="btn-detail" title="Detail" href="'.CRUDBooster::adminpath("warehouse_inventories/detail/".$row["id"]).'"><i class="fa fa-eye"></i></a>';
					return $actionBtn;
				})
				->rawColumns(['action'])
				->toJson();
			}
			// return DataTables::of($data['result'])->make(true);
		}

		public function getDownload($folder) {
			$file = storage_path("app/{$folder}/ExportWarehouseInventory.csv");
			$batchId = session()->get('lastWarehouseInventoryBatchId');
			$batchInfo = DB::table('job_batches')->where('id', $batchId)->first();
			if(file_exists($file)){
				if($batchInfo->pending_jobs == 0){
					session()->forget('lastWarehouseInventoryBatchId');
					session()->forget('folderWarehouseInventory');
					return response()->streamDownload(function () use ($file, $folder) {
						$stream = fopen($file, 'r');
						fpassthru($stream);
						fclose($stream);
						File::deleteDirectory(storage_path("app/{$folder}"));
					},$stream, [
						'Content-Type' => $file,
						'Content-Disposition' => 'attachment; filename="ExportWarehouseInventory-'.date('Y-m-d H:i:s').'.csv"',
					]);
				}else{
					return CRUDBooster::redirect(CRUDBooster::adminPath('warehouse_inventories'),'Generate file not finish!', 'danger');
				}
			}else{
				return CRUDBooster::redirect(CRUDBooster::adminPath('warehouse_inventories'),'Already downloaded!', 'danger');
			}
			
		}
	}