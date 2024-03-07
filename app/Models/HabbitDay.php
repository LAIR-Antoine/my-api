<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabbitDay extends Model
{
    use HasFactory;

    protected $table = 'habbit_day';

    protected $fillable = [
        'habbit_id',
        'day_id',
        'time'
    ];
}
