<?php namespace App\Http\Controllers;

use App\Models\ReportPrivilege;
use App\Models\ReportType;
    use App\Models\UserPrivilege;
    use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;

	class AdminReportPrivilegesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "table_name";
			$this->limit = "20";
			$this->orderby = "cms_privileges_id,asc";
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
			$this->table = "report_privileges";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Report Type","name"=>"report_types_id","join"=>"report_types,report_type"];
			$this->col[] = ["label"=>"Privilege","name"=>"cms_privileges_id","join"=>"cms_privileges,name"];
			$this->col[] = ["label"=>"Table Name","name"=>"table_name"];
			// $this->col[] = ["label"=>"Report Query","name"=>"report_query"];
            $this->col[] = ["label"=>"Report Header","name"=>"report_header"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
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
		public function hook_before_edit(&$postdata, $id) {
			$reportPrivilege = ReportPrivilege::find($id);
		
			$existingRecord = ReportPrivilege::where('report_types_id', request()->report_types)
				->where('cms_privileges_id', request()->cms_privileges)
				->where('table_name', request()->table_name)
				->where('id', '!=', $id)
				->first();
			
		
			if ($existingRecord) {
				return CRUDBooster::redirect(CRUDBooster::mainpath(),'Cannot update. Another record with the same Report Type, Privilege, and Table Name already exists.', 'danger');
			};
		
			$reportPrivilege->report_types_id = request()->report_types;
			$reportPrivilege->cms_privileges_id = request()->cms_privileges;
			$reportPrivilege->table_name = request()->table_name;
		
			$reportQuery = [];
			$reportHeader = [];
		
			foreach (request()->table_columns as $key => $value) {
				array_push($reportQuery, $key);
				array_push($reportHeader, $value);
			}
		
			$reportPrivilege->report_query = implode("`,`", $reportQuery);
			$reportPrivilege->report_header = implode(",", $reportHeader);
		
			$reportPrivilege->save();
		
			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Record updated successfully']);
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

        public function getAdd() {

            if(!CRUDBooster::isCreate()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

            $data = [];
            $data['page_title'] = 'Report Privilege';
            $data['report_types'] = ReportType::active();
            $data['privileges'] = CRUDBooster::isSuperAdmin() ? UserPrivilege::all() : UserPrivilege::privileges();
            $data['tables'] = [
                'store_sales'=>'STORE SALES',
                'digits_sales'=>'DIGITS SALES',
                'store_inventories'=>'STORE INVENTORY',
                'warehouse_inventories'=>'WAREHOUSE INVENTORY'
            ];



            // dd($columNames);

            return view('report-privilege.create',$data);
        }

		public function getEdit($id) {

			$data['report_types'] = ReportType::active();
            $data['privileges'] = CRUDBooster::isSuperAdmin() ? UserPrivilege::all() : UserPrivilege::privileges();
            $data['tables'] = [
                'store_sales'=>'STORE SALES',
                'digits_sales'=>'DIGITS SALES',
                'store_inventories'=>'STORE INVENTORY',
                'warehouse_inventories'=>'WAREHOUSE INVENTORY'
            ];

			$data['report_privilege'] = ReportPrivilege::where('id', $id)->first();
			$queryArray = explode(',', $data['report_privilege']->report_query);
			foreach ($queryArray as &$query) {
				$query = str_replace('`', '', $query);
			}
			$headerArray = explode(',', $data['report_privilege']->report_header);
			
			$data['user_report'] = config('user-report.'.$data['report_privilege']->table_name);
			
			$data['reports'] = array_combine($queryArray, $headerArray);
			
			return view('report-privilege.edit',$data);
		}

		public function getDetail($id) {

			$data['report_privilege'] = ReportPrivilege::where('id', $id)->first();

			$data['report_type'] = DB::table('report_types')->where('id' ,$data['report_privilege']->report_types_id)->value('report_type');
			$data['privilege'] = DB::table('cms_privileges')->where('id' ,$data['report_privilege']->cms_privileges_id)->value('name');
			
			$headerArray = explode(',', $data['report_privilege']->report_header);
			$queryArray = explode(',', $data['report_privilege']->report_query);
			foreach ($queryArray as &$query) {
			$query = str_replace('`', '', $query);
			}
			$data['reports'] = array_combine($queryArray, $headerArray);
			return view('report-privilege.details',$data);
		}

        public function getTableColumns(Request $request){
            return config('user-report.'.$request->tableName);

            // return DB::connection()
            //     ->getDoctrineSchemaManager()
            //     ->listTableColumns($request->tableName.'_report');
        }

        public function saveReport(Request $request) {

            $request->validate([
                'report_types' => 'required',
                'cms_privileges' => 'required',
                'table_name' => 'required'

            ]);

            $reportQuery = [];
            $reportHeader = [];

            foreach ($request->table_columns as $key => $value) {
                array_push($reportQuery, $key);
                array_push($reportHeader, $value);
            }

            ReportPrivilege::updateOrCreate([
                'report_types_id'=>$request->report_types,
                'cms_privileges_id'=>$request->cms_privileges,
                'table_name'=>$request->table_name
            ],[
                'report_types_id'=>$request->report_types,
                'cms_privileges_id'=>$request->cms_privileges,
                'table_name'=>$request->table_name,
                'report_query' => implode("`,`",$reportQuery),
                'report_header' => implode(",",$reportHeader)

            ]);

            return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Saved!']);
        }




	}