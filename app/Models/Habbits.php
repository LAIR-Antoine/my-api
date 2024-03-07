<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habbits extends Model
{
    use HasFactory;

    protected $table = 'habbits';

    protected $dates = ['begin_date', 'end_date'];

    protected $fillable = [
        'name',
        'frequency',
        'type',
        'info'
    ];

    public function days()
    {
        return $this->belongsToMany(Days::class, 'habbit_day', 'habbit_id', 'day_id')->withPivot('time');
    }
}
