<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DistanceGoal;

class DistanceGoalController extends Controller
{
    public function index()
    {
        $distanceGoals = DistanceGoal::all();

        return view('distance_goal.index', compact('distanceGoals'));
    }

    public function create()
    {
        return view('distance_goal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sport' => 'required',
            'distance_to_do' => 'required',
            'distance_done' => 'required',
            'begin_date' => 'required',
            'end_date' => 'required',
            'state' => 'required',
        ]);

        DistanceGoal::create($request->all());

        return redirect()->route('distance_goal')
            ->with('success', 'DistanceGoal created successfully.');
    }

    public function show(DistanceGoal $distance_goal)
    {
        return view('distance_goal', compact('distance_goal'));
    }

    public function edit(DistanceGoal $distance_goal)
    {
        return view('distance_goal.edit', compact('distance_goal'));
    }

    public function update(Request $request, DistanceGoal $distance_goal)
    {
        $request->validate([
            'sport' => 'required',
            'distance_to_do' => 'required',
            'distance_done' => 'required',
            'begin_date' => 'required',
            'end_date' => 'required',
            'state' => 'required',
        ]);
        $distance_goal->update($request->all());

        return redirect()->route('distance_goal')
            ->with('success', 'DistanceGoal updated successfully');
    }

    public function destroy(DistanceGoal $distance_goal)
    {
        $distance_goal->delete();

        return redirect()->route('distance_goal')
            ->with('success', 'DistanceGoal deleted successfully');
    }

    public function welcome()
    {
        $activeGoals = DistanceGoal::where('state', 'active')->orderBy('begin_date', 'asc')->get();

        foreach ($activeGoals as $goal) {
            $goal->avancement = round($goal->distance_done / $goal->distance_to_do * 100);

            $days = (strtotime($goal->end_date) - strtotime($goal->begin_date)) / (60 * 60 * 24) + 1;
            $daysPast = (strtotime(date('Y-m-d')) - strtotime($goal->begin_date)) / (60 * 60 * 24) + 1;
            $timeProportionDone = $daysPast / $days;
            $distanceShouldBeDone = $goal->distance_to_do * $timeProportionDone;
            $goal->gapGoal = round($goal->distance_done - $distanceShouldBeDone, 1);
            $today = date('Y-m-d');
            if ($today < $goal->begin_date) {
                $goal->gapGoal = 0;
            } else if ($today > $goal->end_date) {
                $goal->gapGoal = $goal->distance_done - $goal->distance_to_do;
            }
            //dd($goal->gapGoal);

        }

        return view('welcome', compact('activeGoals'));
    }
}
