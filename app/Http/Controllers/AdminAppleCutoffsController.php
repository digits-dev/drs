<?php namespace App\Http\Controllers;

	use Session;
	use DB;
	use CRUDBooster;
	use App\AppleCutoff;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Http\Request;
	use Excel;

	class AdminAppleCutoffsController extends \crocodicstudio\crudbooster\controllers\CBController {

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
			$this->button_export = true;
			$this->table = "apple_cutoffs";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Sold Date","name"=>"sold_date"];
			$this->col[] = ["label"=>"Day","name"=>"day_fy"];
			$this->col[] = ["label"=>"Year","name"=>"year_fy"];
			$this->col[] = ["label"=>"Quarter","name"=>"quarter_fy"];
			$this->col[] = ["label"=>"Week","name"=>"week_fy"];
			$this->col[] = ["label"=>"Apple Yr Qtr Wk","name"=>"apple_yr_qtr_wk"];
			$this->col[] = ["label"=>"From Date","name"=>"from_date"];
			$this->col[] = ["label"=>"To Date","name"=>"to_date"];
			$this->col[] = ["label"=>"Apple Week Cutoff","name"=>"apple_week_cutoff"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Sold Date','name'=>'sold_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Day','name'=>'day_fy','type'=>'select2-custom1','validation'=>'required|min:1|max:30','width'=>'col-sm-4','datatable'=>'days,day,id'];
			$this->form[] = ['label'=>'Year','name'=>'year_fy','type'=>'select2-custom1','validation'=>'required|min:1|max:30','width'=>'col-sm-4','datatable'=>'year_fy,year,id'];
			$this->form[] = ['label'=>'Quarter','name'=>'quarter_fy','type'=>'select2-custom1','validation'=>'required|min:1|max:30','width'=>'col-sm-4','datatable'=>'quarter_fy,quarter,id'];
			$this->form[] = ['label'=>'Week','name'=>'week_fy','type'=>'select2-custom1','validation'=>'required|min:1|max:30','width'=>'col-sm-4','datatable'=>'week_fy,week,id'];
			$this->form[] = ['label'=>'Apple Yr Qtr Wk','name'=>'apple_yr_qtr_wk','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','readonly'=>true];
			$this->form[] = ['label'=>'From Date','name'=>'from_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'To Date','name'=>'to_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Apple Week Cutoff','name'=>'apple_week_cutoff','type'=>'text','validation'=>'required|min:1|max:100','width'=>'col-sm-4','readonly'=>true];
			
			if(CRUDBooster::getCurrentMethod() == 'getEdit' || CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-4','dataenum'=>'ACTIVE;INACTIVE'];
			}
			if(CRUDBooster::getCurrentMethod() == 'getDetail'){
				$this->form[] = ["label"=>"Created By","name"=>"created_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Created Date','name'=>'created_at', 'type'=>'datetime'];
				$this->form[] = ["label"=>"Updated By","name"=>"updated_by",'type'=>'select',"datatable"=>"cms_users,name"];
				$this->form[] = ['label'=>'Updated Date','name'=>'updated_at', 'type'=>'datetime'];
			}
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
				$this->index_button[] = ['label' => 'Upload Apple Cutoff', "url" => CRUDBooster::mainpath("import-apple"), "icon" => "fa fa-upload", "color"=>"warning"];
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
            $this->script_js = "
			$(document).ready(function() {
				
				$('#sold_date, #from_date, #to_date').datepicker({ 
                    startDate: 'today',
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                });
    
				$('#day_fy').change(function () {
					var day_fy = $(this).val();
				});

				$('#year_fy').change(function () {
					var yr_fy = $(this).val();
					var qtr_fy = $('#quarter_fy').val();
					var wk_fy = $('#week_fy').val();

					$('#apple_yr_qtr_wk').val(yr_fy+' '+qtr_fy+' '+wk_fy);

				});

				$('#quarter_fy').change(function () {
					var yr_fy = $('#year_fy').val();
					var qtr_fy = $(this).val();
					var wk_fy = $('#week_fy').val();

					$('#apple_yr_qtr_wk').val(yr_fy+' '+qtr_fy+' '+wk_fy);
					
				});


				$('#week_fy').change(function () {
					var yr_fy = $('#year_fy').val();
					var qtr_fy = $('#quarter_fy').val();
					var wk_fy = $(this).val();

					$('#apple_yr_qtr_wk').val(yr_fy+' '+qtr_fy+' '+wk_fy);
					
				});

				$('#from_date').on('change',function(){
					var date1 = $(this).val();
					var date2 =$('#to_date').val();

					$('#apple_week_cutoff').val(date1+' to '+date2);
				
				});


				$('#to_date').on('change',function(){
					var date1 =$('#from_date').val();
					var date2 = $(this).val();

					$('#apple_week_cutoff').val(date1+' to '+date2);
				
				});
				
			});
			";

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
	        $postdata['created_by']=CRUDBooster::myId();
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
	        $postdata['updated_by']=CRUDBooster::myId();

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
	    
	    public function importPage() {
	    	$data['page_title'] = 'Upload Apple Cutoff';

	    	return view('upload_apple',$data);
	    }

		public function importTemplate() {
			Excel::create('apple-cutoff-format-'.date("Ymd").'-'.date("h.i.sa"), function ($excel) {
				$excel->sheet('apple', function ($sheet) {
					$sheet->row(1, array('SOLD DATE', 'DAY', 'APPLE FY', 'QUARTER', 'WEEK', 'FROM', 'TO'));
					$sheet->row(2, array('YYYY-MM-DD', 'SATURDAY', '2022', 'Q2', 'WK10', 'YYYY-MM-DD', 'YYYY-MM-DD'));
			}); 	
			})->download('csv');	
	    }


		public function importExcel(Request $request) {

			ini_set('max_execution_time', 0);
			ini_set('memory_limit',"-1");

	    	$insert = array();
	    	$data_saved = false;

			$file = $request->file('import_file');
			
			$validator = \Validator::make(
				[
					'file' => $file,
					'extension' => strtolower($file->getClientOriginalExtension()),
				],
				[
					'file' => 'required',
					'extension' => 'required|in:csv',
				]
			);

			if ($validator->fails()) {
				CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Please check excel format."), 'danger');
			}


	    	if ($request->hasFile('import_file')) {
				$cnt_fail = 0;
				$item_counter = 0;
				$path = $request->file('import_file')->getRealPath();
				$up_option = $request->input('upload_option');

				$data = Excel::load($path, function ($reader) {
				})->get();
		
				$dataExcel = Excel::load($path, function($reader) {
					$reader->noHeading()->all();			
				})->skip(1)->get();
				
		
				foreach ($dataExcel as $key => $value) {

					if (empty($value[0])) {
						$cnt_fail++;
					}
					if (empty($value[1])) {
						$cnt_fail++;
					}
					if (empty($value[2])) {
						$cnt_fail++;
					}
					if (empty($value[3])) {
						$cnt_fail++;
					}
					if (empty($value[4])) {
						$cnt_fail++;
					}
					if (empty($value[5])) {
						$cnt_fail++;
					}
					if (empty($value[6])) {
						$cnt_fail++;
					}

				}

				if($cnt_fail == 0) {

						foreach ($data as $key => $value) {
						    
						  //  $existingData = AppleCutoff::where('sold_date',$value->sold_date)->first();
						  //  if(empty($existingData)){

    							AppleCutoff::updateOrInsert(['sold_date' => $value->sold_date],[
    								'sold_date' =>  		$value->sold_date,
    								'day_fy' =>  			$value->day,
    								'year_fy' =>   			$value->apple_fy,
    								'quarter_fy' =>  		$value->quarter,
    								'week_fy' =>  			$value->week,
    								'apple_yr_qtr_wk' =>  $value->apple_fy.' '.$value->quarter.' '.$value->week,
    								'from_date' =>  		$value->from,
    								'to_date' =>  			$value->to,
    								'apple_week_cutoff' =>  $value->from.' to '.$value->to,
    								'created_by' =>  		CRUDBooster::myId(),
    								'created_at' =>  		date('Y-m-d H:i:s')
    							]);
						  //  }
						  //  else{
						  //      AppleCutoff::where('sold_date',$value->sold_date)->update([
    				// 				'day_fy' =>  			$value->day,
    				// 				'year_fy' =>   			$value->apple_fy,
    				// 				'quarter_fy' =>  		$value->quarter,
    				// 				'week_fy' =>  			$value->week,
    				// 				'apple_yr_qtr_wk' =>  $value->apple_fy.' '.$value->quarter.' '.$value->week,
    				// 				'from_date' =>  		$value->from,
    				// 				'to_date' =>  			$value->to,
    				// 				'apple_week_cutoff' =>  $value->from.' to '.$value->to,
    				// 				'updated_by' =>  		CRUDBooster::myId(),
    				// 				'updated_at' =>  		date('Y-m-d H:i:s')
    				// 			]);
						  //  }

						}

					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Apple cuttoff upload successful!"), 'success');

				}else{
					CRUDBooster::redirect(CRUDBooster::mainpath(), trans("Please check your file."), 'danger');
				}

			}
	    }


	}