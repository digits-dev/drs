<?php namespace App\Http\Controllers;

    use App\Models\Channel;
    use App\Models\Concept;
	use App\Models\StoreSale;
	use App\Models\StoreSalesReport;
	use App\Models\ReportPrivilege;
    use Session;
    use DB;
    use CRUDBooster;
    use Illuminate\Http\Request;
	use Svg\Tag\Rect;
	use Illuminate\Support\Facades\Response;
	use Illuminate\Support\Facades\Storage;
	use File;

	class AdminStoreSalesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "reference_number";
			$this->limit = "20";
			$this->orderby = "reference_number,desc";
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
			$this->button_export = false;
			$this->table = "store_sales";
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
			# END FORM DO NOT REMOVE THIS LINE


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
				$this->index_button[] = ['label'=>'Export Order','url'=>"javascript:showOrderExport()",'icon'=>'fa fa-download'];
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
            $query->whereBetween('sales_date',['2023-06-01','2023-06-03']);
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
        //     $data['page_title'] = 'Store Sales';
        //     $data['channels'] = Channel::active();
        //     $data['result'] = DB::table('store_sales_report')
		// 	->paginate(10);

        //     return view('store-sales.report',$data);
        // }

		public function getIndex() {

			if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			
			$data = [];
			$data['page_title'] = 'Store Sales';
			$data['channels'] = Channel::active();
			$data['concepts'] = Concept::active();
	
			$data['result'] = StoreSale::where('is_final', 1)->paginate(10);
			$ids = $data['result']->pluck('id')->toArray();
			$data['rows'] = StoreSale::generateReport($ids)->get();
			return view('store-sales.report', $data);
			
		}

		public function getDetail($id) {
			if (!CRUDBooster::isRead()) {
				return CRUDBooster::redirect(CRUDBooster::mainPath(), trans('crudbooster.denied_access'));
			}
			$data = [];
			$data['page_title'] = 'Store Sales Details';
			$data['store_sales_details'] = StoreSale::generateReport([$id])->first();
			$data['report_privilege'] = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());

			$headerArray = explode(',', $data['report_privilege']->report_header);
			$queryArray = explode(',', $data['report_privilege']->report_query);
			foreach ($queryArray as &$query) {
				$query = str_replace('`', '', $query);
			}
			
			$data['report'] = array_combine($queryArray, $headerArray);
			return view('store-sales.details',$data);
		}

		public function filterStoreSales(Request $request) {
			ini_set('memory_limit', '-1');
        	ini_set('max_execution_time', 3000);
			$data['searchval'] = $request->search;
			$data['receipt_number'] = $request->receipt_number;
			$data['channels_id'] = $request->channels_id;
			$data['datefrom'] = $request->datefrom;
			$data['dateto'] = $request->dateto;
			$data['concepts_id'] = $request->concepts_id;
			$data['result'] = StoreSale::filterForReport(StoreSale::generateReport(), $request->all())
				->where('is_final', 1)
				->paginate(10);
		
			$data['result']->appends($request->except(['_token']));
			return view('store-sales.filtered-report',$data);
		}

		public function concepts(Request $request) {
			$concept_ids = DB::table('customers')
			->where('channels_id', $request->channel)
			->pluck('concepts_id');
			$concepts = DB::table('concepts')
			->whereIn('id', $concept_ids)
			->orderBy('concept_name', 'asc')
			->get();
			return response()->json($concepts);
		}

		public function getDownload($folder) {
			$file = storage_path("app/{$folder}/ExportStoreSales.csv");
			$batchId = session()->get('lastBatchId');
			$batchInfo = DB::table('job_batches')->where('id', $batchId)->first();
			if(file_exists($file)){
				if($batchInfo->pending_jobs == 0){
					session()->forget('lastBatchId');
					session()->forget('folder');
					return response()->streamDownload(function () use ($file, $folder) {
						$stream = fopen($file, 'r');
						fpassthru($stream);
						fclose($stream);
						File::deleteDirectory(storage_path("app/{$folder}"));
						
					},$stream, [
						'Content-Type' => $file,
						'Content-Disposition' => 'attachment; filename="ExportStoreSales-'.date('Y-m-d H:i:s').'.csv"',
					]);
				}else{
					return CRUDBooster::redirect(CRUDBooster::adminPath('store_sales'),'Generate file not finish!', 'danger');
				}
			}else{
				return CRUDBooster::redirect(CRUDBooster::adminPath('store_sales'),'Already downloaded!', 'danger');
			}
			
		}

	}