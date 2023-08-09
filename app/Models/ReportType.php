<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class ReportType extends Model
{
    use HasFactory;

    protected $table = 'report_types';

    protected $fillable = [
        'report_type',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status','ACTIVE')->get();
    }

    public function scopeByName($query, $name)
    {
        $reportType = $query->where('report_type',$name)
            ->where('status','ACTIVE')->first();

        return $reportType->id;
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
