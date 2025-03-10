<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
   
    public function parks()
    {
        return $this->hasManyThrough(Park::class, Training::class, 'activity_id', 'id', 'id', 'park_id');
    }
    public function users()
{
    return $this->belongsToMany(User::class, 'activity_user');
}
}

