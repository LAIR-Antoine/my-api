<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistanceGoal extends Model
{
    use HasFactory;

    protected $table = 'distance_goal';

    protected $fillable = [
        'sport',
        'distance_to_do',
        'distance_done',
        'begin_date',
        'end_date',
        'state',
    ];
    
}
