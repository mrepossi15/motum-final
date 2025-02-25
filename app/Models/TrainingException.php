<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingException extends Model
{
    use HasFactory;

    protected $fillable = ['training_schedule_id', 'date', 'start_time', 'end_time', 'status'];

    public function schedule()
    {
        return $this->belongsTo(TrainingSchedule::class, 'training_schedule_id');
    }
}