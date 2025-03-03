<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Park extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'opening_hours',
        'location', // Nuevo campo
        'photo_urls',
        'rating', 
        'reviews',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'park_user');
    }
  
    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function activities()
    {
        return $this->hasManyThrough(Activity::class, Training::class, 'park_id', 'id', 'id', 'activity_id')->distinct();
    }
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
    public function reviews()
{
    return $this->hasMany(ParkReview::class);
}
}


