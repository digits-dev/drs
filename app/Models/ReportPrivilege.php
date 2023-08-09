<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class ReportPrivilege extends Model
{
    use HasFactory;

    protected $table = 'report_privileges';

    protected $fillable = [
        'report_types_id',
        'cms_privileges_id',
        'table_name',
        'report_query',
        'report_header',
        'status'
    ];

    public function scopeMyReport($query, $report_type, $privilege)
    {
        return $query->where([
            'status'=> 'ACTIVE',
            'report_types_id'=> $report_type,
            'cms_privileges_id'=> $privilege
        ])->first();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $model->created_by = CRUDBooster::myId();
        });
        static::updating(function($model)
        {
            $model->updated_by = CRUDBooster::myId();
        });
    }
}
