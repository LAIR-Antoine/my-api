<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'strava_id',
        'name',
        'type',
        'start_date_local',
        'location',
        'distance',
        'moving_time',
        'elapsed_time',
        'total_elevation_gain',
        'average_speed',
        'max_speed',
        'average_heartrate',
        'max_heartrate',
        'average_cadence',
        'average_watts',
        'max_watts',
        'suffer_score'
    ];

}
