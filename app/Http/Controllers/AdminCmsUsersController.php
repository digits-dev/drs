<?php namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use DB;
use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Hash;
use CRUDBooster;
use Carbon\Carbon;
use App\Mail\EmailResetPassword;
use Mail;
use Illuminate\Support\Str;
use App\Models\Users;

class AdminCmsUsersController extends CBController {


	public function cbInit() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';
		$this->button_import 	   = FALSE;
		$this->button_export 	   = FALSE;
		if(CRUDBooster::isSuperadmin()) {
		    $this->button_add = true;
		}else{
			$this->button_add = false;
		}
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
		if(CRUDBooster::isSuperadmin()){
			$this->form[] = array("label"=>"Name","name"=>"name",'validation'=>'required|alpha_spaces|min:3');
			$this->form[] = array("label"=>"Email","name"=>"email",'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId());
			$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'required|image|max:1000','resize_width'=>90,'resize_height'=>90);
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'validation'=>'required');
			$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not changed");
		}else{
			$this->form[] = array("label"=>"Name","name"=>"name",'validation'=>'required|alpha_spaces|min:3', 'readonly'=>true);
			$this->form[] = array("label"=>"Email","name"=>"email",'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(), 'readonly'=>true);
			$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'required|image|max:1000','resize_width'=>90,'resize_height'=>90, 'readonly'=>true);
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'validation'=>'required', 'readonly'=>true);
		}
		# END FORM DO NOT REMOVE THIS LINE

	}

	public function getProfile() {

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;
		$this->button_save    = FALSE;
		$this->hide_form 	  = ['id_cms_privileges'];

		$data['page_title'] = cbLang("label_button_profile");
		$data['row']        = CRUDBooster::first('cms_users',CRUDBooster::myId());

        return $this->view('crudbooster::default.form',$data);
	}
	public function hook_before_edit(&$postdata,$id) {

	}
	public function hook_before_add(&$postdata) {

	}

	public function showChangeForcePasswordForm(){
		$data['page_title'] = 'Change Password';
		return view('user-account.change-force-password-form',$data);
	}

	public function postUpdatePassword(Request $request) {
		$fields = $request->all();
		$user = DB::table('cms_users')->where('id',$fields['user_id'])->first();
	
		if (Hash::check($fields['current_password'], $user->password)){
			//Check if password exist in history
			$passwordHistory = DB::table('cms_password_histories')->where('cms_user_id',$fields['user_id'])->get()->toArray();
			$isExist = array_column($passwordHistory, 'cms_user_old_pass');
			if(!self::checkPasswordInArray($fields['new_password'], $isExist)) {
				$validator = \Validator::make($request->all(), [
					'current_password' => 'required',
					'new_password' => 'required',
					'confirm_password' => 'required|same:new_password'
				]);
			
				if ($validator->fails()) {
					return redirect()->to('admin/statistic_builder/dashboard')
							->withErrors($validator)
							->withInput();
				}
				DB::table('cms_users')->where('id', $fields['user_id'])
				->update([
					'password'=>Hash::make($fields['new_password']),
					'last_password_updated' => Carbon::now()->format('Y-m-d'),
					'waiver_count' => 0
				]);
				$newPass = DB::table('cms_users')->where('id',$fields['user_id'])->first();
				Session::put('admin-password', $newPass->password);
				Session::put('check-user',false);
				//Save password history
				DB::table('cms_password_histories')->insert([
					'cms_user_id' => $newPass->id,
					'cms_user_old_pass' => $newPass->password,
					'created_at' => date('Y-m-d h:i:s')
				]);

				return response()->json(['message' => 'Password Updated, You Will Be Logged-Out.', 'status'=>'success']);
			}else{
				return response()->json(['message' => 'Password already used! Please try another password', 'status'=>'error']);
			}
		}else{
			return response()->json(['message' => 'Incorrect Current Password.', 'status'=>'error']);
		}
		
	}

	public function waiveChangePassword(Request $request){
		$user = DB::table('cms_users')->where('id',CRUDBooster::myId())->first();
		DB::table('cms_users')->where('id', CRUDBooster::myId())
			->update([
				'last_password_updated' => Carbon::now()->format('Y-m-d'),
				'waiver_count' => DB::raw('COALESCE(waiver_count, 0) + 1')
			]);
		Session::put('admin-password', $user->password);
		Session::put('check-user',false);
		return response()->json(['message' => 'Waive completed!', 'status'=>'success']);
	}

	public function checkPassword(Request $request) {
		$data = [];
		$fields = $request->all();
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
		$fields = $request->all();
		$user = DB::table('cms_users')->where('id',$fields['id'])->first();
		if ($user->waiver_count === 4){
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

	public function showChangePassword(){
		$data['page_title'] = 'Change Password';
		return view('user-account.change-password',$data);
	}

	//RESET PASSWORD
	public function postSendEmailResetPassword(Request $request){
		$key = Str::random(32);
        $iv = Str::random(16);
        
        $emailExist = DB::table('cms_users')->where('email',$request->email)->exists();
        if(!$emailExist){
			return redirect()->route('getForgot')->with('message', trans("passwords.user"), 'danger');
		}
        $encryptedEmail = openssl_encrypt($request->email, 'aes-256-cbc', $key, 0, $iv);
        $encryptedEmailBase64 = base64_encode($encryptedEmail);

        session(['encryption_key' => $key, 'encryption_iv' => $iv]);
       
        $cleanEncryptedEmail = str_replace('/', '_', $encryptedEmailBase64);

		Mail::to($request->email)
		->send(new EmailResetPassword($cleanEncryptedEmail));
		return redirect()->route('getLogin')->with('message', trans("passwords.sent"),'success');
	}

	public function getResetView($email){
		$data['page_title'] = 'Reset Password Form';
		$data['email'] = $email;
		return view('user-account.reset-password',$data);
    }

	public function postSaveResetPassword(Request $request){
		$key = session('encryption_key');
        $iv = session('encryption_iv');

        if (!$key || !$iv) {
            return json_encode(["message"=>"Request expired, please request another one", "status"=>"error", 'redirect'=>url('admin/login')]);
        }

        $encryptedEmail = base64_decode(str_replace('_', '/', $request->email));
        $decryptedEmail = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $key, 0, $iv);
	
        if ($decryptedEmail === false) {
            return json_encode(["message"=>"Request expired, please request another one", "status"=>"error", 'redirect'=>url('admin/login')]);
        }
		//Check if password exist in history
		$user = DB::table('cms_users')->where('email',$decryptedEmail)->first();
		$passwordHistory = DB::table('cms_password_histories')->where('cms_user_id',$user->id)->get()->toArray();
		$isExist = array_column($passwordHistory, 'cms_user_old_pass');

		if(!self::checkPasswordInArray($request->get('new_password'), $isExist)) {
			$user = Users::where('email', $decryptedEmail)->first();
			$request->validate([
				'new_password' => 'required',
				'confirm_password' => 'required|same:new_password'
			]);

			$user->waiver_count = 0;
			$user->	last_password_updated = now();
			$user->password = Hash::make($request->get('new_password'));
			$user->save();

			DB::table('cms_password_histories')->insert([
				'cms_user_id' => $user->id,
				'cms_user_old_pass' => $user->password,
				'created_at' => date('Y-m-d h:i:s')
			]);

			session()->forget('encryption_key');
			session()->forget('encryption_iv');
			return json_encode(["message"=>"Password successfully reset, you will be redirect to login!", "status"=>"success", 'redirect'=>url('admin/login')]);
		}else{
			return json_encode(["message"=>"Password not available, please try another one!"]);
		}
	}

	
}
