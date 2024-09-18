<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Hash;
use CRUDbooster;
use Carbon\Carbon;
class AdminCmsUsersController extends CBController {


	public function cbInit() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';
		$this->button_import 	   = FALSE;
		$this->button_export 	   = FALSE;
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name",'validation'=>'required|alpha_spaces|min:3');
		$this->form[] = array("label"=>"Email","name"=>"email",'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId());
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'required|image|max:1000','resize_width'=>90,'resize_height'=>90);
		$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'validation'=>'required');
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not changed");
		# END FORM DO NOT REMOVE THIS LINE

	}

	public function getProfile() {

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;
		$this->hide_form 	  = ['id_cms_privileges'];

		$data['page_title'] = cbLang("label_button_profile");
		$data['row']        = CRUDBooster::first('cms_users',CRUDBooster::myId());

        return $this->view('crudbooster::default.form',$data);
	}
	public function hook_before_edit(&$postdata,$id) {

	}
	public function hook_before_add(&$postdata) {

	}

	public function postUpdatePassword(Request $request) {
		$fields = Request::all();
		$user = DB::table('cms_users')->where('id',$fields['user_id'])->first();
		if($fields['type'] == 1){
			if (Hash::check($fields['current_password'], $user->password)){
				//Check if password exist in history
				$passwordHistory = DB::table('cms_password_histories')->where('cms_user_id',$fields['user_id'])->get()->toArray();
				$isExist = array_column($passwordHistory, 'cms_user_old_pass');
				if(!self::checkPasswordInArray($fields['new_password'], $isExist)) {
					$validatedData = Request::validate([
						'current_password' => 'required',
						'new_password' => 'required',
						'confirm_password' => 'required|same:new_password'
					]);
					DB::table('cms_users')->where('id', $fields['user_id'])
					->update([
						'password'=>Hash::make($fields['new_password']),
						'last_password_updated' => now()->format('Y-m-d'),
						'waiver_count' => 0
					]);
					$newPass = DB::table('cms_users')->where('id',$fields['user_id'])->first();
					Session::put('admin_password', $newPass->password);
					$passwordLastUpdated = Carbon::parse($newPass->last_password_updated);
					if ($passwordLastUpdated->diffInMonths(Carbon::now()) > 3) {
						Session::put('password_is_old', $newPass->last_password_updated);
					}else{
						Session::put('password_is_old', '');
					}
					
					//Save password history
					DB::table('cms_password_histories')->insert([
						'cms_user_id' => $newPass->id,
						'cms_user_old_pass' => $newPass->password,
						'created_at' => date('Y-m-d h:i:s')
					]);

					session()->flash('message_type', 'success');
					session()->flash('message', 'Password Updated, You Will Be Logged-Out.');
					return redirect()->to('admin/statistic_builder/dashboard')->with('info', 'Password Updated, You Will Be Logged-Out.');
				}else{
					session()->flash('message_type', 'danger_exist');
					session()->flash('message', 'Password already useed! Please try another password');
					return redirect()->to('admin/statistic_builder/dashboard')->with('danger', 'Password already useed! Please try another password');
				}
			}else{
				session()->flash('message_type', 'danger');
				session()->flash('message', 'Incorrect Current Password.');
				return redirect()->to('admin/statistic_builder/dashboard')->with('danger', 'Incorrect Current Password.');
			}
		}else{
			DB::table('cms_users')->where('id', $fields['user_id'])
			->update([
				'last_password_updated' => now()->format('Y-m-d'),
				'waiver_count' => DB::raw('COALESCE(waiver_count, 0) + 1')
			]);
			$newPass = DB::table('cms_users')->where('id',$fields['user_id'])->first();
			Session::put('admin_password', $newPass->password);
			$passwordLastUpdated = Carbon::parse($newPass->last_password_updated);
			if ($passwordLastUpdated->diffInMonths(Carbon::now()) > 3) {
				Session::put('password_is_old', $newPass->last_password_updated);
			}else{
				Session::put('password_is_old', '');
			}
			session()->flash('message_type', 'info');
			session()->flash('message', 'Waive completed!');
			return redirect()->to('admin/statistic_builder/dashboard')->with('info', 'Waive completed!');
		}
	}

	public function checkPassword(Request $request) {
		$data = [];
		$fields = Request::all();
		$user = DB::table('cms_users')->where('id',$fields['id'])->first();
		if (Hash::check($fields['password'], $user->password)){
			$data['items'] = 1;
		}else{
			$data['items'] = 0;
		}
	
		return json_encode($data);
	}

	public function checkWaive(Request $request) {
		$data = [];
		$fields = Request::all();
		$user = DB::table('cms_users')->where('id',$fields['id'])->first();
		if ($user->waiver_count === 3){
			$data['items'] = 0;
		}else{
			$data['items'] = 1;
		}
	
		return json_encode($data);
	}

	// Function to check if the new password matches any hashed password
	function checkPasswordInArray($newPassword, $hashedPasswords) {
		foreach ($hashedPasswords as $hashedPassword) {
			if (Hash::check($newPassword, $hashedPassword)) {
				return true; // Password exists in the array
			}
		}
		return false; // Password does not exist
	}
}
