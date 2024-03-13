<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Days;
use App\Models\Habbits;
use App\Models\HabbitDay;
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
        }])->where('type', 'active')->get();
    
        return view('habbits', compact('days', 'habits'));
    }

    public function create()
    {
        $habits = Habbits::all();
        return view('habbits.create', compact('habits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|date',
            'habbits' => 'nullable|array',
            'habbits.*' => 'exists:habbits,id',
            'times' => 'nullable|array',
        ]);
    
        $day = Days::firstOrCreate(['date' => $request->day]);
    
        // Optionnel : nettoyer les habitudes non sélectionnées pour ce jour
        $day->habbits()->whereNotIn('habbit_id', $request->get('habbits', []))->detach();

        // Traitement des habitudes avec ou sans temps
        foreach ($request->get('habbits', []) as $habitId) {
            $time = $request->times[$habitId] ?? null;
            $day->habbits()->syncWithoutDetaching([$habitId => ['time' => $time]]);
        }
    
        return redirect()->route('habbits')->with('success', 'Habitudes mises à jour avec succès.');
    }
    

    public function getHabitsForDay($date)
    {
        $day = Days::whereDate('date', $date)->first();
        $habits = [];

        if ($day) {
            $habits = $day->habbits->map(function ($habit) {
                return ['habbit_id' => $habit->id];
            });
        }

        return response()->json($habits);
    }
}
