<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class AppleCutoff extends Model
{
    use HasFactory;

    protected $table = 'apple_cutoffs';

    protected $fillable = [
        'sold_date',
        'day_fy',
        'year_fy',
        'quarter_fy',
        'week_fy',
        'apple_yr_qtr_wk',
        'from_date',
        'to_date',
        'apple_week_cutoff',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status','ACTIVE')->where('sold_date','>',Carbon::now()->subMonths(3)->format("Y-m-d"))->get();
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
