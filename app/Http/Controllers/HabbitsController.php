<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Days;
use App\Models\Habbits;
use Carbon\Carbon;

class HabbitsController extends Controller
{
    public function index()
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(99);
    
        $days = Days::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
    
        $habits = Habbits::with(['days' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc');
        }])->get();
    
        return view('habbits', compact('days', 'habits'));
    }

}
