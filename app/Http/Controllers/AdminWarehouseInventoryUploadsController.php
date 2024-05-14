<?php namespace App\Http\Controllers;

use App\Exports\WarehouseInventoryUploadBatchExport;
use App\Jobs\UpdateBatchImportStatusJob;
use App\Models\ReportPrivilege;
use App\Models\WarehouseInventoriesReport;
use App\Models\WarehouseInventory;
use App\Models\WarehouseInventoryUpload;
use Session;
use Request;
use DB;
use CRUDBooster;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

	class AdminWarehouseInventoryUploadsController extends \crocodicstudio\crudbooster\controllers\CBController {
		public $status_class = [
			'FILE UPLOADED' => 'label-warning',
			'IMPORTING' => 'label-info',
			'IMPORT FINISHED' => 'label-primary',
			'IMPORT FAILED' => 'label-danger',
			'FILE DOWNLOADED' => 'label-info',
			'GENERATING FILE' => 'label-warning',
			'FILE GENERATED' => 'label-primary',
			'FINAL' => 'label-success',
			'FAILED TO GENERATE FILE' => 'label-danger',
		];

		public $allowed_privs_to_tag_as_final = [1,3];

	    public function cbInit() {
	    	# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->table 			   = "warehouse_inventory_uploads";	        
			$this->title_field         = "file_name";
			$this->limit               = 20;
			$this->orderby             = "id,desc";
			$this->show_numbering      = false;
			$this->global_privilege    = false;	        
			$this->button_table_action = true;   
			$this->button_action_style = "button_icon";     
			$this->button_add          = false;
			$this->button_delete       = false;
			$this->button_edit         = false;
			$this->button_detail       = true;
			$this->button_show         = true;
			$this->button_filter       = false;        
			$this->button_export       = false;	        
			$this->button_import       = false;
			$this->button_bulk_action  = true;	
			$this->sidebar_mode		   = "normal"; //normal,mini,collapse,collapse-mini
			# END CONFIGURATION DO NOT REMOVE THIS LINE						      

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$status_class = $this->status_class;
			$this->col[] = ["label"=>"Batch","name"=>"batch"];
			$this->col[] = ["label"=>"Status","name"=>"status","callback"=>function($row) use ($status_class) {
				$class = $status_class[$row->status] ?? 'label-default';
				return "<label class='label $class'>$row->status</label>";			
			}];
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
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Importing Started","name"=>"id","callback"=>fn ($row) => $row->importing_started_at ? date('Y-m-d H:i:s', $row->importing_started_at) : null];
			$this->col[] = ["label"=>"Importing Finished","name"=>"id","callback"=>fn ($row) =>$row->importing_finished_at ? date('Y-m-d H:i:s', $row->importing_finished_at) : null];
			$this->col[] = ["label"=>"Tagged By","name"=>"tagged_as_final_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Tagged Date","name"=>"tagged_as_final_at"];
			
			# END COLUMNS DO NOT REMOVE THIS LINE
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ["label"=>"Batch","name"=>"batch","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Chunk Count","name"=>"chunk_count","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"File Name","name"=>"file_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"File Path","name"=>"file_path","type"=>"textarea","required"=>TRUE,"validation"=>"required|string|min:5|max:5000"];
			$this->form[] = ["label"=>"Folder Name","name"=>"folder_name","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Headings","name"=>"headings","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Is Final","name"=>"is_final","type"=>"radio","required"=>TRUE,"validation"=>"required|integer","dataenum"=>"Array"];
			$this->form[] = ["label"=>"Job Batches Id","name"=>"job_batches_id","type"=>"select2","required"=>TRUE,"validation"=>"required|min:1|max:255","datatable"=>"job_batches,name"];
			$this->form[] = ["label"=>"Row Count","name"=>"row_count","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Status","name"=>"status","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			$this->form[] = ["label"=>"Tagged As Final At","name"=>"tagged_as_final_at","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			$this->form[] = ["label"=>"Tagged As Final By","name"=>"tagged_as_final_by","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];

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
			$this->addaction[] = [
				'title'=>'Generate File',
				'url'=>CRUDBooster::mainpath('generate-file/[id]'),
				'icon'=>'fa fa-file',
				'color' => 'info',
				'showIf' => '(
					[importing_finished_at] && 
					[status] != "IMPORT FAILED" && 
					[status] != "GENERATING FILE" && 
					![generated_file_path]
				)'
			];
			$this->addaction[] = [
				'title'=>'Export Batch',
				'url'=>CRUDBooster::mainpath('export-batch/[id]'),
				'target'=>"_blank",
				'icon'=>'fa fa-download',
				'color' => 'success',
				'showIf' => '([importing_finished_at] && [generated_file_path])'
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
			if (in_array(CRUDBooster::myPrivilegeId(), $this->allowed_privs_to_tag_as_final)) {
				$this->button_selected[] = ['label'=>'TAG AS FINAL','icon'=>'fa fa-thumbs-up','name'=>'tag_as_final'];
			}
	                
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
			if (CRUDBooster::getCurrentMethod() == 'getIndex') {
					$this->index_button[] = [
						"title"=>"Upload Inventory",
						"label"=>"Upload Inventory",
						"icon"=>"fa fa-upload",
						"color"=>"success",
						"url"=>route('warehouse-inventory.upload-view')
					];
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
	        $this->post_index_html = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
			$this->load_js[] = asset('js/import-progress.js');

	        
	        
	        
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
					$batch = WarehouseInventoryUpload::find($id);
					$batch = $batch->getBatchDetails();
					if (!$batch->finished_at) {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch # $batch->batch has not finished importing.", 'danger');
					}
					if ($batch->is_final) {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch # $batch->batch is already tagged as final.", 'danger');
					}
					if ($batch->status == 'IMPORT FAILED') {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Batch # $batch->batch has failed importing.", 'danger');
					}
					if ($batch->status != 'FILE DOWNLOADED') {
						return CRUDBooster::redirect(CRUDBooster::mainPath(), "Please download and check first the batch # $batch->batch before tagging as final.", 'danger');
					}

					$batch->update([
						'is_final' => 1,
						'status' => 'FINAL',
						'tagged_as_final_at' => date('Y-m-d H:i:s'), 
						'tagged_as_final_by' => CRUDBooster::myId(),
					]);
					$warehouse_inventory = WarehouseInventory::where('batch_number', $batch->batch)->update(['is_final' => 1]);
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
				->leftJoin('job_batches', 'job_batches.id', 'warehouse_inventory_uploads.job_batches_id')
				->addSelect(
					'warehouse_inventory_uploads.*',
					'job_batches.created_at as importing_started_at',
					'job_batches.finished_at as importing_finished_at',
					'job_batches.cancelled_at as importing_cancelled_at',
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

		public function generateFile($id) {
			$batch = WarehouseInventoryUpload::find($id);
			$folder_name = $batch->folder_name;
			$batch_export = new WarehouseInventoryUploadBatchExport($batch->batch);
			$filename = $batch->batch. '.csv';
			$report_type = 'warehouse-inventory-upload';
			$excel_path = storage_path("app/$report_type/$folder_name/$filename");
			$excel_file = $batch_export->store("$report_type/$folder_name/$filename");
			$excel_file->chain([new UpdateBatchImportStatusJob($batch, $excel_path)]);
			return CRUDBooster::redirect(CRUDBooster::mainPath(), "Generating file for batch #$batch->batch.", 'info');
		}

		public function exportBatch($id) {
			$batch = WarehouseInventoryUpload::find($id);
			if (file_exists($batch->generated_file_path)) {
				if ($batch->status != 'FINAL') {
					$batch->update(['status' => 'FILE DOWNLOADED']);
				}
				return response()->download($batch->generated_file_path);
			} else {
				abort(404, 'File not found');
			}
		}

		public function downloadUploadedFile($id) {
			$batch = WarehouseInventoryUpload::find($id);

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
			$warehouse_inventory_upload = (new WarehouseInventoryUpload())->getBatchDetails($id);
			$user_report = ReportPrivilege::myReport(4, CRUDBooster::myPrivilegeId());

			$warehouse_inventories = WarehouseInventory::filterForReport(WarehouseInventory::generateReport(), ['search' => $search_term], true)
				->where('batch_number', $warehouse_inventory_upload->batch)
				->orderBy('reference_number', 'ASC')
				->paginate(10)
				->appends(['search' => $search_term]);

			$data = [];
			$data['item'] = $warehouse_inventory_upload;
			$data['user_report'] = $user_report;
			$data['warehouse_inventories'] = $warehouse_inventories;
			$data['search_term'] = $search_term;

			return $this->view('warehouse-inventory-upload.details', $data);
		}



	}