<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'backup_path',
        'created_at'
    ];

    public $timestamps = false;

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
