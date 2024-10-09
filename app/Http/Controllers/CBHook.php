<?php 
namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use CRUDBooster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function postLogin()
    {

        $validator = \Validator::make(Request::all(), [
            'email' => 'required|email|exists:'.config('crudbooster.USER_TABLE'),
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }

        $email = Request::input("email");
        $password = Request::input("password");
        $users = DB::table(config('crudbooster.USER_TABLE'))->where("email", $email)->first();

        if (\Hash::check($password, $users->password)) {
            $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();

            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
			
			if($users->status == "INACTIVE"){
                CRUDBooster::insertLog(trans("crudbooster.try_log_login",['email'=>$users->email,'ip'=>Request::server('REMOTE_ADDR')]));
				Session::flush();
                return redirect()->route('getLogin')->with(['message', "Account Doesn't Exist/Deactivated",'message_type' => 'danger']);
            }

            $moduls_sales_reports_id = DB::table('cms_moduls')->where('table_name','sales_reports')->value('id');
			$moduls_sales_digits_reports_id = DB::table('cms_moduls')->where('table_name','sales_digits_reports')->value('id');
			$moduls_store_inv_reports_id = DB::table('cms_moduls')->where('table_name','store_inventory_reports')->value('id');
			$moduls_warehouse_inv_reports_id = DB::table('cms_moduls')->where('table_name','warehouse_inventory_reports')->value('id');
			
			$column_sales_reports_views = DB::table('view_settings')->where([
                'cms_privileges_id' => $users->id_cms_privileges,
                'cms_moduls_id' => $moduls_sales_reports_id
            ])->get();
            
            $column_sales_digits_reports_views = DB::table('view_settings')->where([
                'cms_privileges_id' => $users->id_cms_privileges,
                'cms_moduls_id' => $moduls_sales_digits_reports_id
            ])->get();
            
            $column_store_inv_reports_views = DB::table('view_settings')->where([
                'cms_privileges_id' => $users->id_cms_privileges,
                'cms_moduls_id' => $moduls_store_inv_reports_id
            ])->get();
            
            $column_warehouse_inv_reports_views = DB::table('view_settings')->where([
                'cms_privileges_id' => $users->id_cms_privileges,
                'cms_moduls_id' => $moduls_warehouse_inv_reports_id
            ])->get();
            
            
            $photo = ($users->photo) ? asset($users->photo) : asset('vendor/crudbooster/avatar.jpg');
            Session::put('admin_id', $users->id);
            Session::put('admin_is_superadmin', $priv->is_superadmin);
            Session::put('admin_name', $users->name);
            Session::put('admin_photo', $photo);
            Session::put('admin_privileges_roles', $roles);
            Session::put("admin_privileges", $users->id_cms_privileges);
            Session::put('admin_privileges_name', $priv->name);
            Session::put('admin_lock', 0);
            Session::put('theme_color', $priv->theme_color);
            Session::put("appname", get_setting('appname'));
            Session::put('column_sales_reports_views', $column_sales_reports_views);
			Session::put('column_sales_digits_reports_views', $column_sales_digits_reports_views);
			Session::put('column_store_inv_reports_views', $column_store_inv_reports_views);
			Session::put('column_warehouse_inv_reports_views', $column_warehouse_inv_reports_views);

            CRUDBooster::insertLog(cbLang("log_login", ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));

			self::afterLogin();

            return redirect(CRUDBooster::adminPath());
        } else {
            return redirect()->route('getLogin')->with(['message'=> cbLang('alert_password_wrong'), 'message_type'=>'danger']);
        }
    }

	public function afterLogin() {
		$user =  DB::table('cms_users')->where("id", CRUDBooster::myId())->first();
        $today = Carbon::now();
        $lastChangePass = Carbon::parse($user->last_password_updated);
        $needsPasswordChange = \Hash::check('qwerty', $user->password) || $lastChangePass->diffInMonths($today) > 3;
        $defaultPass = \Hash::check('qwerty', $user->password);
        if($needsPasswordChange){
            Log::debug("message: {$needsPasswordChange}");
            Session::put('check-user',true);
            Session::put('admin-password',$user->password);
            return redirect()->route('show-change-force-password')->send();
        }
	}
}