<?php namespace App\Http\Controllers;

use App\Exports\SalesUploadBatchExport;
use App\Models\ReportPrivilege;
use App\Models\StoreSale;
use App\Models\StoreSalesReport;
use App\Models\StoreSalesUpload;
use App\Models\StoreSalesUploadLine;
use Session;
use Request;
use DB;
use CRUDBooster;
use Maatwebsite\Excel\Facades\Excel;

	class AdminStoreSalesUploadsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "folder_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
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
			$this->table = "store_sales_uploads";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Batch","name"=>"batch"];
			$this->col[] = ["label"=>"Uploaded File","name"=>"file_name","callback"=>function($row) {
				$main_path = CRUDBooster::mainPath();
				return "<div>$row->file_name</div> <a class='pull-right' title='Download File' href='$main_path/download-uploaded-file/$row->id' target='_blank'/><i class='fa fa-download'></i></a>";
			}];
			$this->col[] = ["label"=>"Import Progress","name"=>"job_batches_id","callback"=>function($row){
				return "
					<div class='import-progress' id='$row->job_batches_id'>
						<span class='import-progress-text'></span>
						<br/>
						<progress class='import-progress-bar' value='0' max='100'></progress>
					</div>
				";
			}];
			$this->col[] = ["label"=>"Row Count","name"=>"row_count"];
			$this->col[] = ["label"=>"Status","name"=>"is_final","callback"=>function($row) {
				if ($row->is_final) {
					return '<label class="label label-success">TAGGED AS FINAL</label>';
				}
				if (!$row->importing_finished_at) {
					return '<label class="label label-warning">IMPORTING STARTED</label>';
				}
				return '<label class="label label-info">IMPORTING FINISHED</label>';
			}];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Importing Started","name"=>"id","callback"=>fn ($row) => $row->importing_started_at ? date('Y-m-d H:i:s', $row->importing_started_at) : null];
			$this->col[] = ["label"=>"Importing Finished","name"=>"id","callback"=>fn ($row) =>$row->importing_finished_at ? date('Y-m-d H:i:s', $row->importing_finished_at) : null];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Batch','name'=>'batch','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'File Name','name'=>'file_name','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'File Path','name'=>'file_path','type'=>'textarea','validation'=>'required|string|min:5|max:5000','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Row Count','name'=>'row_count','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Headings','name'=>'headings','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'select2','validation'=>'required|min:1|max:255','width'=>'col-sm-10','datatable'=>'cms_users,name'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Batch","name"=>"batch","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Job Batches Id","name"=>"job_batches_id","type"=>"select2","required"=>TRUE,"validation"=>"required|min:1|max:255","datatable"=>"job_batches,name"];
			//$this->form[] = ["label"=>"Folder Name","name"=>"folder_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"File Name","name"=>"file_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"File Path","name"=>"file_path","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			//$this->form[] = ["label"=>"Row Count","name"=>"row_count","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Chunk Count","name"=>"chunk_count","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Headings","name"=>"headings","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
			$this->addaction[] = [
				'title'=>'Export Batch',
				'url'=>CRUDBooster::mainpath('export-batch/[id]'),
				'target'=>"_blank",
				'icon'=>'fa fa-download',
				'color' => 'success',
				'showIf' => '[importing_finished_at]'
			];


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
			$this->button_selected[] = ['label'=>'TAG AS FINAL','icon'=>'fa fa-thumbs-up','name'=>'tag_as_final'];


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
			$this->load_js[] = asset('js/sales-upload.js');



	        /*
	        | ----------------------------------------------------------------------
	        | Add css style at body
	        | ----------------------------------------------------------------------
	        | css code in the variable
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = "
				.import-progress-bar {
					width: 100%;
				}
			";



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
	        if ($button_name == 'tag_as_final') {
				foreach ($id_selected as $id) {
					$batch = StoreSalesUpload::find($id);
					$batch = $batch->getBatchDetails();
					if (!$batch->finished_at) {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch # $batch->batch is still importing...", 'danger');
					}
					if ($batch->is_final) {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch # $batch->batch is already tagged as final.", 'danger');
					}
					$batch->update([
						'is_final' => 1,
						'tagged_as_final_at' => date('Y-m-d H:i:s'), 
						'tagged_as_final_by' => CRUDBooster::myId(),
					]);
					$store_sales = StoreSale::where('batch_number', $batch->batch)->update(['is_final' => 1]);
				}
			}

			return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch successfully tagged as final.", 'success');

	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate query of index result
	    | ----------------------------------------------------------------------
	    | @query = current sql query
	    |
	    */
	    public function hook_query_index(&$query) {
	        $query
				->leftJoin('job_batches', 'job_batches.id', 'store_sales_uploads.job_batches_id')
				->addSelect(
					'store_sales_uploads.file_path',
					'job_batches.created_at as importing_started_at',
					'job_batches.finished_at as importing_finished_at',
				);


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

		public function exportBatch($id) {
			$batch = StoreSalesUpload::find($id);
			return Excel::download(new SalesUploadBatchExport($batch->batch), "$batch->batch.xlsx");
		}

		public function downloadUploadedFile($id) {
			$batch = StoreSalesUpload::find($id);

			if (file_exists($batch->file_path)) {
				return response()->download($batch->file_path);
			} else {
				abort(404, 'File not found');
			}
		}

		public function getDetail($id) {
			if (!CRUDBooster::isRead()) {
				return CRUDBooster::redirect(CRUDBooster::mainPath(), trans('crudbooster.denied_access'));
			}
			$search_term = request('search');
			$store_sale_upload = (new StoreSalesUpload())->getBatchDetails($id);
			$user_report = ReportPrivilege::myReport(1, 3);
			$store_sales = StoreSalesReport::filter(['search' => $search_term])
				->selectRaw("`$user_report->report_query`")
				->where('batch_number', $store_sale_upload->batch)
				->paginate(10)
				->appends(['search' => $search_term]);

			$data = [];
			$data['item'] = $store_sale_upload;
			$data['user_report'] = $user_report;
			$data['store_sales'] = $store_sales;
			$data['search_term'] = $search_term;

			return $this->view('store-sales-upload.details', $data);
		}

	}
