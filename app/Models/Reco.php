<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reco extends Model
{
    use HasFactory;

    protected $table = 'reco';

    protected $fillable = [
        'name',
        'instagram',
        'youtube',
        'spotify',
        'strava',
        'type',
        'is_favorite'
    ];
}
