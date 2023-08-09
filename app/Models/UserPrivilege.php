<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrivilege extends Model
{
    use HasFactory;

    protected $table = 'cms_privileges';

    protected $fillable = [
        'name',
        'is_superadmin'
    ];

    public function scopePrivileges($query)
    {
        return $query->where('is_superadmin',0)->get();
    }
}
