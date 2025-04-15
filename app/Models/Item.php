<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Si querés relacionarlo con entrenamientos
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_items');
    }
}