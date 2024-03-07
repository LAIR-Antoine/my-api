<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Days extends Model
{
    use HasFactory;

    protected $table = 'days';

    protected $fillable = [
        'date',
        'number',
    ];

    public function habbits()
    {
        return $this->belongsToMany(Habbits::class, 'habbit_day', 'day_id', 'habbit_id')->withPivot('time');
    }
}
