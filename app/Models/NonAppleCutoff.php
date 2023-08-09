<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class NonAppleCutoff extends Model
{
    use HasFactory;

    protected $table = 'non_apple_cutoffs';

    protected $fillable = [
        'sold_date',
        'day_cy',
        'year_cy',
        'month_cy',
        'week_cy',
        'non_apple_yr_mon_wk',
        'from_date',
        'to_date',
        'non_apple_week_cutoff',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status','ACTIVE')->get();
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
