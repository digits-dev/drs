<?php namespace App\Http\Controllers;

use App\Exports\RunRateExport;
use Session;
use Illuminate\Http\Request;
use DB;
use CRUDBooster;
use App\Models\Channel;
use App\Models\RunRate;
use App\Models\StoreSalesReport;
use Maatwebsite\Excel\Facades\Excel;

	class AdminRunRateController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "store_sales";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Apple Week Cutoff","name"=>"apple_week_cutoff"];
			$this->col[] = ["label"=>"Apple Yr Qtr Wk","name"=>"apple_yr_qtr_wk"];
			$this->col[] = ["label"=>"Batch Date","name"=>"batch_date"];
			$this->col[] = ["label"=>"Batch Number","name"=>"batch_number"];
			$this->col[] = ["label"=>"Channels Id","name"=>"channels_id","join"=>"channels,channel_name"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by"];
			$this->col[] = ["label"=>"Current Srp","name"=>"current_srp"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Apple Week Cutoff','name'=>'apple_week_cutoff','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Apple Yr Qtr Wk','name'=>'apple_yr_qtr_wk','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Batch Date','name'=>'batch_date','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Batch Number','name'=>'batch_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Channels Id','name'=>'channels_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'channels,channel_name'];
			$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Current Srp','name'=>'current_srp','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Customer Location','name'=>'customer_location','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Customers Id','name'=>'customers_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'customers,customer_name'];
			$this->form[] = ['label'=>'Digits Code','name'=>'digits_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Digits Code Rr Ref','name'=>'digits_code_rr_ref','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Dtp Ecom','name'=>'dtp_ecom','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Dtp Rf','name'=>'dtp_rf','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Employees Id','name'=>'employees_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'employees,employee_name'];
			$this->form[] = ['label'=>'Is Final','name'=>'is_final','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'Array'];
			$this->form[] = ['label'=>'Is Valid','name'=>'is_valid','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'Array'];
			$this->form[] = ['label'=>'Item Code','name'=>'item_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Landed Cost','name'=>'landed_cost','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Net Sales','name'=>'net_sales','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Non Apple Week Cutoff','name'=>'non_apple_week_cutoff','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Non Apple Yr Mon Wk','name'=>'non_apple_yr_mon_wk','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Organizations Id','name'=>'organizations_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'organizations,organization_name'];
			$this->form[] = ['label'=>'Qtysold Csrp','name'=>'qtysold_csrp','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Qtysold Ecom','name'=>'qtysold_ecom','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Qtysold Lc','name'=>'qtysold_lc','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Qtysold Price','name'=>'qtysold_price','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Qtysold Rf','name'=>'qtysold_rf','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Qtysold Sc','name'=>'qtysold_sc','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Quantity Sold','name'=>'quantity_sold','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Receipt Number','name'=>'receipt_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Reference Number','name'=>'reference_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Report Types Id','name'=>'report_types_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'report_types,id'];
			$this->form[] = ['label'=>'Rr Flag','name'=>'rr_flag','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Rr Flag Temp','name'=>'rr_flag_temp','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sale Memo Reference','name'=>'sale_memo_reference','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sales Date','name'=>'sales_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sales Date Yr Mo','name'=>'sales_date_yr_mo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sales Month','name'=>'sales_month','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sales Transaction Types Id','name'=>'sales_transaction_types_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'sales_transaction_types,id'];
			$this->form[] = ['label'=>'Sales Year','name'=>'sales_year','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Sold Price','name'=>'sold_price','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Store Cost','name'=>'store_cost','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Systems Id','name'=>'systems_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'systems,system_name'];
			$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Apple Week Cutoff","name"=>"apple_week_cutoff","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Apple Yr Qtr Wk","name"=>"apple_yr_qtr_wk","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Batch Date","name"=>"batch_date","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Batch Number","name"=>"batch_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Channels Id","name"=>"channels_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"channels,channel_name"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Current Srp","name"=>"current_srp","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Customer Location","name"=>"customer_location","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Customers Id","name"=>"customers_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"customers,customer_name"];
			//$this->form[] = ["label"=>"Digits Code","name"=>"digits_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Digits Code Rr Ref","name"=>"digits_code_rr_ref","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Dtp Ecom","name"=>"dtp_ecom","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Dtp Rf","name"=>"dtp_rf","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Employees Id","name"=>"employees_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"employees,employee_name"];
			//$this->form[] = ["label"=>"Is Final","name"=>"is_final","type"=>"radio","required"=>TRUE,"validation"=>"required|integer","dataenum"=>"Array"];
			//$this->form[] = ["label"=>"Is Valid","name"=>"is_valid","type"=>"radio","required"=>TRUE,"validation"=>"required|integer","dataenum"=>"Array"];
			//$this->form[] = ["label"=>"Item Code","name"=>"item_code","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Item Description","name"=>"item_description","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Landed Cost","name"=>"landed_cost","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Net Sales","name"=>"net_sales","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Non Apple Week Cutoff","name"=>"non_apple_week_cutoff","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Non Apple Yr Mon Wk","name"=>"non_apple_yr_mon_wk","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Organizations Id","name"=>"organizations_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"organizations,organization_name"];
			//$this->form[] = ["label"=>"Qtysold Csrp","name"=>"qtysold_csrp","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Qtysold Ecom","name"=>"qtysold_ecom","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Qtysold Lc","name"=>"qtysold_lc","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Qtysold Price","name"=>"qtysold_price","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Qtysold Rf","name"=>"qtysold_rf","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Qtysold Sc","name"=>"qtysold_sc","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Quantity Sold","name"=>"quantity_sold","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Receipt Number","name"=>"receipt_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Reference Number","name"=>"reference_number","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Report Types Id","name"=>"report_types_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"report_types,id"];
			//$this->form[] = ["label"=>"Rr Flag","name"=>"rr_flag","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Rr Flag Temp","name"=>"rr_flag_temp","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Sale Memo Reference","name"=>"sale_memo_reference","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Sales Date","name"=>"sales_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
			//$this->form[] = ["label"=>"Sales Date Yr Mo","name"=>"sales_date_yr_mo","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Sales Month","name"=>"sales_month","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Sales Transaction Types Id","name"=>"sales_transaction_types_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"sales_transaction_types,id"];
			//$this->form[] = ["label"=>"Sales Year","name"=>"sales_year","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Sold Price","name"=>"sold_price","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Store Cost","name"=>"store_cost","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Systems Id","name"=>"systems_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"systems,system_name"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			# OLD END FORM

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
	        $this->alert        = array();
	                

	        
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

		public function getIndex() {
			$data = [];
			$data['page_title'] = 'Digits Reports System';
			$data['years'] = StoreSalesReport::distinctYears();
			$data['channels'] = Channel::active();
			
	
			return view('run-rate.run-rate', $data);
		}

		public function filterRunRate(Request $request) {
			$request = $request->all();
			[$brand, $cutoff_type] = explode(' - ', $request['brand']);
			$search = $request['search'];
			$is_apple = (int) ($brand === 'APPLE');
			$query_filter_params = self::generateFilterParams($request, $is_apple);

			if ($cutoff_type === 'WEEKLY') {
				$cutoff = $request['cutoff'];
			} else {
				$cutoff = $request['sales_year']. '_'. $request['sales_month'];
			}
			$cutoff_data = self::getCutoffData($cutoff_type, $cutoff, $is_apple);
			$rows = RunRate::filterRunRate($query_filter_params, $cutoff_data['cutoff_queries'], $search)
				->paginate(10)
				->appends($request);
			
			$data = [];
			$data['page_title'] = 'Digits Reports System';
			$data['query_filter_params'] = $query_filter_params;
			$data['rows'] = $rows;
			$data['cutoff_columns'] = $cutoff_data['cutoff_columns'];
			$data['search'] = $search;

			return $this->view('run-rate.filter-run-rate', $data);
		}

		public function generateFilterParams($request, $is_apple) {
			$query_filter_params = [];
			$query_filter_params[] = ['is_apple', '=', $is_apple];

			if ($request['sales_year']) {
				$query_filter_params[] = ['sales_year', '=', $request['sales_year']];
			}
			if ($request['sales_month']) {
				$query_filter_params[] = ['sales_month', '=', $request['sales_month']];
			}
			if ($request['channel_name']) {
				$query_filter_params[] = ['channel_name', '=', $request['channel_name']];
			}
			if ($request['store_concept_name']) {
				$query_filter_params[] = ['store_concept_name', '=', $request['store_concept_name']];
			}
			if ($request['customer_location']) {
				$query_filter_params[] = ['customer_location', '=', $request['customer_location']];
			}
			return $query_filter_params;
		}

		public function getCutoffData($cutoff_type, $cutoff, $is_apple) {
			$cutoff_queries = [];
			if ($cutoff_type === 'WEEKLY') {
				$column_name = $is_apple ? 'apple_week_cutoff' : 'non_apple_week_cutoff';
				$last_12 = RunRate::where('is_apple', $is_apple)
					->where('apple_week_cutoff', '<=', $cutoff)
					->distinct('apple_week_cutoff')
					->orderBy('apple_week_cutoff', 'desc')
					->limit(12)
					->pluck('apple_week_cutoff')
					->toArray();
			} else {
				$column_name = 'sales_date_yr_mo';
				$last_12 = RunRate::where('is_apple', $is_apple)
					->where('sales_date_yr_mo', '<=', $cutoff)
					->distinct('sales_date_yr_mo')
					->orderBy('sales_date_yr_mo', 'desc')
					->limit(12)
					->pluck('sales_date_yr_mo')
					->toArray();

			}

			foreach ($last_12 as $last_12_item) {
				$cutoff_queries[] = DB::raw("SUM(CASE WHEN $column_name = '$last_12_item' THEN quantity_sold ELSE 0 END) as `$last_12_item`");
			}
			return [
				'cutoff_queries' => $cutoff_queries,
				'cutoff_columns' => $last_12,
			];
		}

		public function exportRunRate(Request $request) {
			$request = $request->all();
			[$brand, $cutoff_type] = explode(' - ', $request['brand']);
			$sales_year = $request['sales_year'];
			$sales_month = $request['sales_month'];
			$search = $request['search'];
			$is_apple = (int) ($brand === 'APPLE');
			$query_filter_params = self::generateFilterParams($request, $is_apple);

			if ($cutoff_type === 'WEEKLY') {
				$cutoff = $request['cutoff'];
			} else {
				$cutoff = $request['sales_year']. '_'. $request['sales_month'];
			}
			$cutoff_data = self::getCutoffData($cutoff_type, $cutoff, $is_apple);
			$query = RunRate::filterRunRate($query_filter_params, $cutoff_data['cutoff_queries'], $search);
			$export = (new RunRateExport($query, $cutoff_data['cutoff_columns']));
			$file_name = implode('-', [$brand, $cutoff_type, $sales_year, $sales_month]);
			return Excel::download($export, "$file_name.xlsx");
		}


	}