<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkReview extends Model
{
    use HasFactory;

    protected $fillable = ['park_id', 'author', 'rating', 'text', 'time'];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }
}

