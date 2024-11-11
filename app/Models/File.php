<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'path', 'status', 'user_id', 'group_id','is_approved'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function versions()
    {
        return $this->hasMany(FileVersion::class);
    }

    public function checkouts()
    {
        return $this->hasMany(FileCheckout::class);
    }
    public function actions()
    {
        return $this->hasMany(FileCheckout::class);
    }
}
