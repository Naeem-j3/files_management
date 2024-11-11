<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name','owner_id'];

    public function files()
    {
        return $this->hasMany(File::class, 'group_id');
    }

    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id')
            ->withPivot('status') // Include the `status` field from the pivot table
            ->wherePivot('status', 'accepted'); // Only get users with 'accepted' status
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
