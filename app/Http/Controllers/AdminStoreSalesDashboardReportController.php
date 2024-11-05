<?php namespace App\Http\Controllers;

	
	use App\Models\Channel;
	use App\Models\Concept;
	use App\Models\SalesDashboardReport;
	use App\Services\SalesDashboardReportService as SSDashboardReportService;
	use Barryvdh\DomPDF\Facade as DomPDF;
	use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
	use Illuminate\Support\Facades\Log;
	use Maatwebsite\Excel\Facades\Excel;
	use DB;
	use CRUDBooster;
	use Exception;
	use Illuminate\Http\Request;

	class AdminStoreSalesDashboardReportController extends \crocodicstudio\crudbooster\controllers\CBController {
		

		protected $dashboardService;

		public function __construct(SSDashboardReportService $dashboardService){
			$this->dashboardService = $dashboardService;
		}

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

		public function getIndex()
		{
			if (!CRUDBooster::isView()) {
				CRUDBooster::redirect(CRUDBooster::adminPath(), trans('crudbooster.denied_access'));
			}

			$data = [];
			$data['page_title'] = 'Store Sales Dashboard Report';
			$data['loading'] = true; 

			
			return view('dashboard-report.store-sales.index', $data);
		}

		// New method to fetch the data asynchronously
		public function fetchData()
		{

			$reloadData = request()->has('reload_data');

			// dd($reloadData);

			$generatedData = $reloadData ? $this->dashboardService->generateSalesReport('store_sales') : $this->dashboardService->getData('store_sales');
			$month = $generatedData['yearData']['month'];

			if($month == 1){

				$prevYear = $generatedData['yearData']['nextPreviousYear'];
				$currYear = $generatedData['yearData']['previousYear'];

				$currYearForDaily = $generatedData['yearData']['currentYear'];

			} else {
				$prevYear = $generatedData['yearData']['previousYear'];
				$currYear = $generatedData['yearData']['currentYear'];
			}

			$channel_codes = $generatedData['channel_codes'];
			$lastThreeDaysDates = $generatedData['lastThreeDaysDates'];
			$channels = Channel::get(['id', 'channel_name']);
			$concepts = Concept::get(['id', 'concept_name']);


			$data = [
				'channel_codes' => $channel_codes,
				'prevYear' => $prevYear,
				'currYear' => $currYear,
				'lastThreeDaysDates' => $lastThreeDaysDates,
			];
			
			// Generate HTML for each tab using partial views
			$tab1Html = view('dashboard-report.partials.daily', [
				'channel_codes' => $channel_codes,
				'prevYear' => $month == 1 ? $currYear : $prevYear,
				'currYear' => $month == 1 ? $currYearForDaily : $currYear,
				'lastThreeDaysDates' => $lastThreeDaysDates,
			])->render();
			
			$tab2Html = view('dashboard-report.partials.monthly',
			 $data)->render();
			 
			$tab3Html = view('dashboard-report.partials.quarterly',
			 $data)->render();

			$tab4Html = view('dashboard-report.partials.ytd', [
				'channel_codes' => $channel_codes,
				'prevYear' => $prevYear,
				'currYear' => $currYear,
				'lastThreeDaysDates' => $lastThreeDaysDates,
				'month' => $month, 
				'channels' => $channels, 
				'concepts' => $concepts,
			])->render();
		
			return response()->json([
				'tab1Html' => $tab1Html,
				'tab2Html' => $tab2Html,
				'tab3Html' => $tab3Html,
				'tab4Html' => $tab4Html,
			]);
		}

		public function saveChart2(Request $request)
		{
			$data = $request->input('image');

			// Remove the data URL prefix
			$data = str_replace('data:image/png;base64,', '', $data);
			$data = str_replace(' ', '+', $data);

			// Prepare the image data URL
			$imageData = 'data:image/png;base64,' . $data;

			try {
				// Prepare additional data for the PDF view
				$pdfData = [
					'chartImage' => $imageData, // Pass the base64 image data directly
					// Add any other data you want to include in the PDF view
				];

				// Load the view and generate the PDF
				$pdf = SnappyPDF::loadView('dashboard-report.store-sales.test-pdf2', $pdfData)
					->setPaper('A4', 'landscape')
					->setOptions(['margin-top' => 35, 'margin-right' => 5, 'margin-bottom' => 10, 'margin-left' => 5]);

				// Return the PDF as a download
				return response()->stream(
					function () use ($pdf) {
						echo $pdf->output();
					},
					200,
					[
						'Content-Type' => 'application/pdf',
						'Content-Disposition' => 'attachment; filename="document.pdf"',
					]
				);
			} catch (\Exception $e) {
				// Handle exceptions and log errors
				Log::error('PDF Generation Error: ' . $e->getMessage());
				return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
			}
		}
	

		public function saveChart(Request $request)
		{
			$images = $request->input('images'); 
			$data = $request->input('data'); 

			function hasManyItems($string, $minCount = 5) {
				return count(array_filter(array_map('trim', explode(',', $string)))) > $minCount;
			}

			function hasManyCharacters($string, $minLength = 160) {
				// Trim the string and check its length
				return strlen(trim($string)) > $minLength;
			}

			$hasManyStore = hasManyCharacters($data['Store']);
			$hasManyChannel = hasManyCharacters($data['Channel']);
			$hasManyBrand = hasManyCharacters($data['Brand']);
			$hasManyCategory = hasManyCharacters($data['Category']);
			$hasManyStoreConcept = hasManyCharacters($data['Store Concept']);
			$hasManyStoreMall = hasManyCharacters($data['Mall']);

			$hasManyValues = $hasManyStore || $hasManyChannel || $hasManyBrand || $hasManyCategory || $hasManyStoreConcept || $hasManyStoreMall;

			// Prepare an array for base64 images
			$pdfData = [];

			foreach ($images as $image) {
				// Remove the data URL prefix
				$data = str_replace('data:image/png;base64,', '', $image);
				$data = str_replace(' ', '+', $data);
				
				// Prepare the image data URL
				$imageData = 'data:image/png;base64,' . $data;
				$pdfData[] = $imageData; // Add to pdfData array
			}

			try {
				// Load the view and generate the PDF with all images
				$pdf = SnappyPDF::loadView('dashboard-report.store-sales.test-pdf2', [
					'chartImages' => $pdfData, 
					'data' => $request->input('data'),
					'hasManyValues' => $hasManyValues,
					])
					->setPaper('A4', 'landscape')
					->setOptions(['margin-top' => 10, 'margin-right' => 5, 'margin-bottom' => 10, 'margin-left' => 5]);

				// Return the PDF as a download
				return response()->stream(
					function () use ($pdf) {
						echo $pdf->output();
					},
					200,
					[
						'Content-Type' => 'application/pdf',
						'Content-Disposition' => 'attachment; filename="document.pdf"',
					]
				);
			} catch (\Exception $e) {
				// Handle exceptions and log errors
				Log::error('PDF Generation Error: ' . $e->getMessage());
				return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
			}
		}

		

	}