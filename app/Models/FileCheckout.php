<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCheckout extends Model
{
    use HasFactory;
    protected $fillable = ['file_id', 'user_id', 'checked_out_at', 'checked_in_at'];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
