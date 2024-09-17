<?php 
namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use CRUDbooster;
use Carbon\Carbon;

class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function afterLogin() {
		$user =  $users = DB::table('cms_users')->where("id", CRUDbooster::myId())->first();
		if ($user->last_password_updated) {
			// Compare the password updated date with the current date
			$passwordLastUpdated = Carbon::parse($user->last_password_updated);
	
			if ($passwordLastUpdated->diffInMonths(Carbon::now()) > 3) {
				// Password is older than 3 months
				Session::put('password_is_old', $users->last_password_updated);
			}else{
				Session::put('password_is_old', '');
			}
		}
		Session::put('admin_password', $users->password);
	}
}