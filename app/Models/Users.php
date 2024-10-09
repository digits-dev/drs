<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //
    protected $table = 'cms_users';
    protected $fillable = [
        'name',
        'first_name',
        'last_name' ,
        'username',
        'photo',
        'email', 
        'id_cms_privileges',
        'password',
        'department_id',
        'company_name_id',
        'location_id',
        'approver_id_manager',
        'approver_id_executive',
        'contact_person',
        'bill_to',
        'customer_location_name',
        'sub_department_id',
        'position_id',
        'approver_id',
        'store_id'
    ] ;

      //customers query
      public function scopeUser($query, $id)
      {
          return $query->where('cms_users.id', $id)
                        ->leftjoin('cms_privileges', 'cms_users.id_cms_privileges','=','cms_privileges.id')
                        ->leftjoin('departments', 'cms_users.department_id','=','departments.id')
                        ->leftjoin('sub_department', 'cms_users.sub_department_id','=','sub_department.id')
                        ->leftjoin('locations', 'cms_users.location_id', '=', 'locations.id')
                        ->leftjoin('cms_users as approver', 'cms_users.approver_id', '=', 'approver.id')
                        ->select(
                            'cms_users.*',
                            'departments.*',
                            'sub_department.*',
                            'locations.*',
                            'cms_privileges.name as privilege_name',
                            'approver.name as approver'
                         
                          ) 
                        ->first();
      }
}
