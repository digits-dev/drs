<?php 
namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use CRUDbooster;
class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function afterLogin() {
		$user =  $users = DB::table('cms_users')->where("id", CRUDbooster::myId())->first();
		Session::put('admin_password', $users->password);
	}
}