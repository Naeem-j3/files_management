<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;


    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'user_id',
        'data',
        'read_at',
    ];


    protected $casts = [
        'data' => 'array', // Automatically casts the JSON data to an array
        'read_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
