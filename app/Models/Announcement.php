<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'message',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(Users::class,'announcement_user')->withTimestamps();
    }
}
