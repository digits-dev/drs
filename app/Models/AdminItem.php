<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminItem extends Model
{
    use HasFactory;
    protected $table = 'admin_items';
    protected $guarded = [];
}
