<?php namespace App\Http\Controllers;

	use App\Exports\WeeklySalesExport;
	use App\Models\StoreSalesDashboardReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
	use Log;
	use Maatwebsite\Excel\Facades\Excel;
	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use Carbon\Carbon;
use Exception;
use QuickChart;

	class AdminStoreSalesDashboardReportController extends \crocodicstudio\crudbooster\controllers\CBController {
		

		protected $myData;

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
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = false;
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
			//$this->form[] = ['label'=>'Apple Week Cutoff','name'=>'apple_week_cutoff','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Apple Yr Qtr Wk','name'=>'apple_yr_qtr_wk','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Batch Date','name'=>'batch_date','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Batch Number','name'=>'batch_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Channels Id','name'=>'channels_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'channels,channel_name'];
			//$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Current Srp','name'=>'current_srp','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Customer Location','name'=>'customer_location','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Customers Id','name'=>'customers_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'customers,customer_name'];
			//$this->form[] = ['label'=>'Digits Code','name'=>'digits_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Digits Code Rr Ref','name'=>'digits_code_rr_ref','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Dtp Ecom','name'=>'dtp_ecom','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Dtp Rf','name'=>'dtp_rf','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Employees Id','name'=>'employees_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'employees,employee_name'];
			//$this->form[] = ['label'=>'Is Final','name'=>'is_final','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'Array'];
			//$this->form[] = ['label'=>'Is Valid','name'=>'is_valid','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'Array'];
			//$this->form[] = ['label'=>'Item Code','name'=>'item_code','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Landed Cost','name'=>'landed_cost','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Net Sales','name'=>'net_sales','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Non Apple Week Cutoff','name'=>'non_apple_week_cutoff','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Non Apple Yr Mon Wk','name'=>'non_apple_yr_mon_wk','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Organizations Id','name'=>'organizations_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'organizations,organization_name'];
			//$this->form[] = ['label'=>'Qtysold Csrp','name'=>'qtysold_csrp','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Qtysold Ecom','name'=>'qtysold_ecom','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Qtysold Lc','name'=>'qtysold_lc','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Qtysold Price','name'=>'qtysold_price','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Qtysold Rf','name'=>'qtysold_rf','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Qtysold Sc','name'=>'qtysold_sc','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Quantity Sold','name'=>'quantity_sold','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Receipt Number','name'=>'receipt_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Reference Number','name'=>'reference_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Report Types Id','name'=>'report_types_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'report_types,id'];
			//$this->form[] = ['label'=>'Rr Flag','name'=>'rr_flag','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Rr Flag Temp','name'=>'rr_flag_temp','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sale Memo Reference','name'=>'sale_memo_reference','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sales Date','name'=>'sales_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sales Date Yr Mo','name'=>'sales_date_yr_mo','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sales Month','name'=>'sales_month','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sales Transaction Types Id','name'=>'sales_transaction_types_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'sales_transaction_types,id'];
			//$this->form[] = ['label'=>'Sales Year','name'=>'sales_year','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Sold Price','name'=>'sold_price','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Store Cost','name'=>'store_cost','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Systems Id','name'=>'systems_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'systems,system_name'];
			//$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
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



	    //By the way, you can still create your own method in here... :) 

		
		public function getIndex() {

			if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			

			$data = [];
			$data['page_title'] = 'Store Sales Dashboard Report';

			$generatedData = self::generateDailySalesReport();

			$data = array_merge($data, $generatedData);

			// dd($data['channel_codes']);

			// dd($data);


			Log::info(json_encode($data, JSON_PRETTY_PRINT));

			return view('dashboard-report.store-sales.index', $data);
		}

		public function generateDailySalesReport(){

			
			// $currentDay = date('d');
			// $currentMonth = date('m');
			// $currentYear = date('Y'); 
			// $previousYear = date('Y', strtotime('-1 year'));
			
			// $currentMonth = 3;
			// $previousYear = 2019;
			// $currentYear = 2020; 
			// $currentDay = 29;

			$currentMonth = 9;
			$previousYear = 2023;
			$currentYear = 2024; 
			$currentDay = 30;


			$years = [
				['year' => $previousYear, 'month' => $currentMonth],
				['year' => $currentYear, 'month' => $currentMonth],
			];
			
			$data = [
				'yearData' => [
					'previousYear' => $years[0]['year'],
					'currentYear' => $years[1]['year'],
				],
				'channel_codes' => [],
			];

			$data['lastThreeDaysDates'] = self::getLastThreeDaysOrDates('date', "{$currentYear}-{$currentMonth}-{$currentDay}");

			foreach ($years as $yearData) {
				self::processYearData($yearData['year'], $yearData['month'], $currentDay, $data);
			}

			// dd($data['channel_codes']);

			return $data;

		}



		function processYearData($year, $month, $day, &$data) {
			$storeSalesDR = new StoreSalesDashboardReport(['year' => $year, 'month' => $month, 'day' => $day]);

			// Create temp table and get summary
			$storeSalesDR->createTempTable();

			// Get and store sales summary
			$data['channel_codes']['TOTAL'][$year]['weeks'] = $storeSalesDR->getSalesSummary()->toArray();

			// Last three days summary
			$data['channel_codes']['TOTAL'][$year]['last_three_days'] = $storeSalesDR->getSalesSummaryForLastThreeDays();

			
			// Process sales per channel
			$sumPerChannel = $storeSalesDR->getSalesWeeklyPerChannel();

	

			foreach ($sumPerChannel as $sale) {
				// if($sale['channel_classification'] == 'OTHER'){
				// 	dump($sale['week_cutoff']);
				// 	dump($sale['min_reference_number']);
				// }
				$channelCode = $sale['channel_classification'];


				if (!isset($data['channel_codes'][$channelCode])) {
					$data['channel_codes'][$channelCode] = [];
				}

				$data['channel_codes'][$channelCode][$year]['weeks'][$sale['week_cutoff']] = [
					'sum_of_net_sales' => $sale['sum_of_net_sales'],
				];
			}
			
			
			// Last three days per channel
			$lastThreeDaysPerChannel = $storeSalesDR->getSalesSummaryForLastThreeDaysPerChannel();

			foreach ($lastThreeDaysPerChannel as $sale) {
				$channelCode = $sale['channel_classification'];

				if (!isset($data['channel_codes'][$channelCode])) {
					$data['channel_codes'][$channelCode] = [];
				}

				$data['channel_codes'][$channelCode][$year]['last_three_days'][] = [
					'date_of_the_day' => $sale['date_of_the_day'],
					'day' => $sale['day'],
					'sum_of_net_sales' => $sale['sum_of_net_sales'],
				];
			}

			$lastThreeDaysDates = $storeSalesDR->getLastThreeDaysDates("{$year}-{$month}-{$day}");

			// Now add entries for any missing dates with a sum_of_net_sales of 0
			foreach ($lastThreeDaysDates as $date) {
				foreach ($data['channel_codes'] as $channelCode => &$years) {
					if (!isset($years[$year]['last_three_days'])) {
						$years[$year]['last_three_days'] = [];
					}

					// Check if the date already exists in last_three_days
					$exists = false;
					foreach ($years[$year]['last_three_days'] as $entry) {
						if ($entry['date_of_the_day'] === $date) {
							$exists = true;
							break;
						}
					}

					// If it doesn't exist, add it with a sum_of_net_sales of 0
					if (!$exists) {
						$years[$year]['last_three_days'][] = [
							'date_of_the_day' => $date,
							'sum_of_net_sales' => 0,
						];
					}
				}
			}

			// Sort by date 
			foreach ($data['channel_codes'] as $channelCode => &$years) {
				foreach ($years as $year => &$yearData) {
					if (isset($yearData['last_three_days'])) {
						usort($yearData['last_three_days'], function($a, $b) {
							return strtotime($a['date_of_the_day']) - strtotime($b['date_of_the_day']);
						});
					}
				}
			}
			

			// Drop the temporary table
			$storeSalesDR->dropTempTable();
		}

		
		public function getLastThreeDaysOrDates($type = 'day', $date = null)
		{
			// Use the provided date or default to today
			$today = $date ? Carbon::parse($date) : Carbon::today();
			
			// Initialize an array to hold the last three previous dates
			$lastThreeDays = [];
			
			// If today is the 1st, 2nd, or 3rd, include those days
			if ($today->day <= 3) {
				for ($i = 0; $i < $today->day; $i++) {
					$day = $today->copy()->subDays($i);
					$lastThreeDays[] = $day; // Store as Carbon objects
				}
			} else {
				// Get the last three days prior to the provided date
				for ($i = 1; $i <= 3; $i++) {
					$day = $today->copy()->subDays($i);
					$lastThreeDays[] = $day; // Store as Carbon objects
				}
			}

			// Sort the array of Carbon objects in ascending order
			usort($lastThreeDays, function($a, $b) {
				return $a->gt($b) ? 1 : -1;
			});
			
			// Format the dates for output
			$formattedDays = [];
			foreach ($lastThreeDays as $day) {
				// $formattedDays[] = $type === 'date' ? $day->format('d-M') : $day->format('D');
				$formattedDays[$day->format('d-M')] = $day->format('D');
			}

			return $formattedDays;
		}
		public function exportPDF()
		{
			$data = [];
			$generatedData = self::generateDailySalesReport();
		
			// Merge the generated data into the data array
			$data = array_merge($data, $generatedData);


			// $chart = new QuickChart(array(
			// 	'width' => 1000,
			// 	'height' => 700,
			//   ));
			  
			//   $chart->setConfig('{
			// 	"type": "line",
			// 	"data": {
			// 	  "labels": ["WEEK 1", "WEEK 2", "WEEK 3", "WEEK 4"],
			// 	  "datasets": [
			// 		{"label": "ECOMM", "data": [19897245, 16476039.83, 14485540, 22433607.900000002], "borderWidth": 1},
			// 		{"label": "TOTAL-RTL", "data": [65506610.190000005, 70960019.11, 71478592, 90899858.93], "borderWidth": 1},
			// 		{"label": "SC", "data": [309880, 450125, 661786, 782352.15], "borderWidth": 1},
			// 		{"label": "DLR/CRP", "data": [5509638.7, 985270.5199999999, 2728548.3, 2277052.06], "borderWidth": 1},
			// 		{"label": "CON", "data": [14972.65, 0, 0, 0], "borderWidth": 1},
			// 		{"label": "FRA-DR", "data": [18280313.79, 18162475, 21202927, 27324391], "borderWidth": 1}
			// 	  ]
			// 	},
			// 	"options": {
			// 	  "title": {
			// 		"display": true,
			// 		"text": "2023"
			// 	  },
			// 	  "scales": {
			// 		"y": {
			// 		  "beginAtZero": true,
			// 		  "ticks": {
			// 			"callback": function(value) {
			// 			  return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			// 			}
			// 		  }
			// 		}
			// 	  }
			// 	}
			//   }');
			  

			// 		$chart2 = new QuickChart(array(
			// 			'width' => 800,
			// 			'height' => 500,
			// 			'format' => 'svg'

			// 		  ));
					
			// 		$chart2->setConfig('{"type":"pie","data":{"labels":["WEEK 1","WEEK 2","WEEK 3","WEEK 4"],"datasets":[{"label":"ECOMM","data":[19897245,16476039.83,14485540,22433607.900000002],"borderWidth":1},{"label":"TOTAL-RTL","data":[65506610.190000005,70960019.11,71478592,90899858.93],"borderWidth":1},{"label":"SC","data":[309880,450125,661786,782352.15],"borderWidth":1},{"label":"DLR/CRP","data":[5509638.7,985270.5199999999,2728548.3,2277052.06],"borderWidth":1},{"label":"CON","data":[14972.65,0,0,0],"borderWidth":1},{"label":"FRA-DR","data":[18280313.79,18162475,21202927,27324391],"borderWidth":1}]},"options":{"title": {
			//   "display": true,
			//   "text": "2024",
			// },"scales":{"y":{"beginAtZero":true}}}}');

			// $data['test1'] = $chart->getShortUrl();
		

			// Load the view and generate the PDF
			$pdf = PDF::loadView('dashboard-report.store-sales.test-pdf', $data)
					   ->setPaper('A4', 'landscape')
					   ->setOptions( [
						   'isHtml5ParserEnabled' => true,
						   'isJavascriptEnabled' => true,
						   'isRemoteEnabled' => true,
						   'defaultFont' => 'Arial',
						   'isFontSubsettingEnabled' => true,
					   ]);
		
			// Return the PDF as a download
			return $pdf->download('document.pdf');
		}
	
		public function showPDF(){
			// dd('test');
			// $data = ['title' => 'Welcome to Laravel PDF'];
			
			
			$data = [];
			$data['page_title'] = 'Store Sales Dashboard Report';

			$generatedData = self::generateDailySalesReport();

			$data = array_merge($data, $generatedData);
			
			return view('dashboard-report.store-sales.test-pdf', $data);
		}

		public function exportExcel(){

			$data = self::generateDailySalesReport();
		
			return Excel::download(new WeeklySalesExport($data), 'custom-excel.xlsx');
		}

		public function showExcel(){
			//
		}



	
	}