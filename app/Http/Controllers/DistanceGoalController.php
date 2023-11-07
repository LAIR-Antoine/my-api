<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DistanceGoal;
use App\Models\Activities;
use Carbon\Carbon;

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
        }

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $pastWeekStart = Carbon::now()->startOfWeek()->subWeek();
        $pastWeekEnd = Carbon::now()->endOfWeek()->subWeek();

        $currentWeekDates = [];
        $pastWeekDates = [];

        for ($date = $currentWeekStart; $date <= $currentWeekEnd; $date->addDay()) {
            $currentWeekDates[] = $date->toDateString();
        }

        for ($date = $pastWeekStart; $date <= $pastWeekEnd; $date->addDay()) {
            $pastWeekDates[] = $date->toDateString();
        }

        $currentWeekActivities = [];
        $pastWeekActivities = [];

        $weekDaysLetter = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

        foreach ($currentWeekDates as $index => $date) {

            $day = date('d', strtotime($date));
            $month = date('m', strtotime($date));
            $currentWeekActivities['dates'][$index] = $weekDaysLetter[$index].' '. $day .'/' . $month;

            $currentWeekActivitiesDB = Activities::where('start_date_local', '>=', $date . ' 00:00:00')->where('start_date_local', '<=', $date . ' 23:59:59')->get();
            foreach ($currentWeekActivitiesDB as $activity) {
                if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                    $currentWeekActivities[1][$index] = $activity->moving_time / 3600;
                } else if ($activity->type == 'Run') {
                    $currentWeekActivities[2][$index] = $activity->moving_time / 3600;
                } else if ($activity->type == 'Swim') {
                    $currentWeekActivities[0][$index] = $activity->moving_time / 3600;
                }
            }
            if (!isset($currentWeekActivities[0][$index])) {
                $currentWeekActivities[0][$index] = 0;
            }
            if (!isset($currentWeekActivities[1][$index])) {
                $currentWeekActivities[1][$index] = 0;
            }
            if (!isset($currentWeekActivities[2][$index])) {
                $currentWeekActivities[2][$index] = 0;
            }
        }
        
        foreach ($pastWeekDates as $index => $date) {

            $day = date('d', strtotime($date));
            $month = date('m', strtotime($date));
            $pastWeekActivities['dates'][$index] = $weekDaysLetter[$index].' '. $day .'/' . $month;
            $pastWeekActivitiesDB = Activities::where('start_date_local', '>=', $date . ' 00:00:00')->where('start_date_local', '<=', $date . ' 23:59:59')->get();
            foreach ($pastWeekActivitiesDB as $activity) {
                if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                    $pastWeekActivities[1][$index] = $activity->moving_time / 3600;
                } else if ($activity->type == 'Run') {
                    $pastWeekActivities[2][$index] = $activity->moving_time / 3600;
                } else if ($activity->type == 'Swim') {
                    $pastWeekActivities[0][$index] = $activity->moving_time / 3600;
                }
            }
            if (!isset($pastWeekActivities[0][$index])) {
                $pastWeekActivities[0][$index] = 0;
            }
            if (!isset($pastWeekActivities[1][$index])) {
                $pastWeekActivities[1][$index] = 0;
            }
            if (!isset($pastWeekActivities[2][$index])) {
                $pastWeekActivities[2][$index] = 0;
            }
        }

        //dd($currentWeekActivities, $pastWeekActivities);



        return view('welcome', compact('activeGoals', 'currentWeekActivities', 'pastWeekActivities'));
    }
}
