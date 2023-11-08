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

        $fiveLastWeeks = $this->getFiveLastWeeksForChart();

        return view('welcome', compact('activeGoals', 'currentWeekActivities', 'pastWeekActivities', 'fiveLastWeeks'));
    }

    public function getFiveLastWeeksForChart() {
        
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $currentWeekNumber = Carbon::now()->weekOfYear;
    
        $pastOneStart = Carbon::now()->startOfWeek()->subWeek(1);
        $pastOneEnd = Carbon::now()->endOfWeek()->subWeek(1);
        $pastOneWeekNumber = Carbon::now()->subWeek(1)->weekOfYear;
    
        $pastTwoStart = Carbon::now()->startOfWeek()->subWeek(2);
        $pastTwoEnd = Carbon::now()->endOfWeek()->subWeek(2);
        $pastTwoWeekNumber = Carbon::now()->subWeek(2)->weekOfYear;

        $pastThreeStart = Carbon::now()->startOfWeek()->subWeek(3);
        $pastThreeEnd = Carbon::now()->endOfWeek()->subWeek(3);
        $pastThreeWeekNumber = Carbon::now()->subWeek(3)->weekOfYear;

        $pastFourStart = Carbon::now()->startOfWeek()->subWeek(4);
        $pastFourEnd = Carbon::now()->endOfWeek()->subWeek(4);
        $pastFourWeekNumber = Carbon::now()->subWeek(4)->weekOfYear;
        
        $fiveLastWeeks['dates'] = [
            'S ' . $pastFourWeekNumber,
            'S ' . $pastThreeWeekNumber,
            'S ' . $pastTwoWeekNumber,
            'S ' . $pastOneWeekNumber,
            'S ' . $currentWeekNumber
        ];

        $pastFourWeekNumberActivities = Activities::where('start_date_local', '>=', $pastFourStart . ' 00:00:00')->where('start_date_local', '<=', $pastFourEnd . ' 23:59:59')->get();
        $pastThreeWeekNumberActivities = Activities::where('start_date_local', '>=', $pastThreeStart . ' 00:00:00')->where('start_date_local', '<=', $pastThreeEnd . ' 23:59:59')->get();
        $pastTwoWeekNumberActivities = Activities::where('start_date_local', '>=', $pastTwoStart . ' 00:00:00')->where('start_date_local', '<=', $pastTwoEnd . ' 23:59:59')->get();
        $pastOneWeekNumberActivities = Activities::where('start_date_local', '>=', $pastOneStart . ' 00:00:00')->where('start_date_local', '<=', $pastOneEnd . ' 23:59:59')->get();
        $currentWeekNumberActivities = Activities::where('start_date_local', '>=', $currentWeekStart . ' 00:00:00')->where('start_date_local', '<=', $currentWeekEnd . ' 23:59:59')->get();

        foreach ($pastFourWeekNumberActivities as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][0] = isset($fiveLastWeeks[1][0]) ? $fiveLastWeeks[1][0] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Run') {
                $fiveLastWeeks[2][0] =  isset($fiveLastWeeks[2][0]) ? $fiveLastWeeks[2][0] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Swim') {
                $fiveLastWeeks[0][0] =  isset($fiveLastWeeks[0][0]) ? $fiveLastWeeks[0][0] : null + ($activity->moving_time / 3600);
            }
        }

        foreach ($pastThreeWeekNumberActivities as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][1] = isset($fiveLastWeeks[1][1]) ? $fiveLastWeeks[1][1] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Run') {
                $fiveLastWeeks[2][1] = isset($fiveLastWeeks[2][1]) ? $fiveLastWeeks[2][1] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Swim') {
                $fiveLastWeeks[0][1] = isset($fiveLastWeeks[0][1]) ? $fiveLastWeeks[0][1] : null + ($activity->moving_time / 3600);
            }
        }

        foreach ($pastTwoWeekNumberActivities as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][2] = isset($fiveLastWeeks[1][2]) ? $fiveLastWeeks[1][2] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Run') {
                $fiveLastWeeks[2][2] = isset($fiveLastWeeks[2][2]) ? $fiveLastWeeks[2][2] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Swim') {
                $fiveLastWeeks[0][2] = isset($fiveLastWeeks[0][2]) ? $fiveLastWeeks[0][2] : null + ($activity->moving_time / 3600);
            }
        }

        foreach ($pastOneWeekNumberActivities as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][3] = isset($fiveLastWeeks[1][3]) ? $fiveLastWeeks[1][3] : null + ($activity->moving_time / 3600);
                var_dump($fiveLastWeeks[1][3], $activity->moving_time);
            } else if ($activity->type == 'Run') {
                $fiveLastWeeks[2][3] = isset($fiveLastWeeks[2][3]) ? $fiveLastWeeks[2][3] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Swim') {
                $fiveLastWeeks[0][3] = isset($fiveLastWeeks[0][3]) ? $fiveLastWeeks[0][3] : null + ($activity->moving_time / 3600);
            }
        }

        foreach ($currentWeekNumberActivities as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][4] = isset($fiveLastWeeks[1][4]) ? $fiveLastWeeks[1][4] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Run') {
                $fiveLastWeeks[2][4] = isset($fiveLastWeeks[2][4]) ? $fiveLastWeeks[2][4] : null + ($activity->moving_time / 3600);
            } else if ($activity->type == 'Swim') {
                $fiveLastWeeks[0][4] = isset($fiveLastWeeks[0][4]) ? $fiveLastWeeks[0][4] : null + ($activity->moving_time / 3600);
            }
        }

        if (!isset($fiveLastWeeks[0][0])) {
            $fiveLastWeeks[0][0] = 0;
        }
        if (!isset($fiveLastWeeks[0][1])) {
            $fiveLastWeeks[0][1] = 0;
        }
        if (!isset($fiveLastWeeks[0][2])) {
            $fiveLastWeeks[0][2] = 0;
        }
        if (!isset($fiveLastWeeks[0][3])) {
            $fiveLastWeeks[0][3] = 0;
        }
        if (!isset($fiveLastWeeks[0][4])) {
            $fiveLastWeeks[0][4] = 0;
        }

        if (!isset($fiveLastWeeks[1][0])) {
            $fiveLastWeeks[1][0] = 0;
        }
        if (!isset($fiveLastWeeks[1][1])) {
            $fiveLastWeeks[1][1] = 0;
        }
        if (!isset($fiveLastWeeks[1][2])) {
            $fiveLastWeeks[1][2] = 0;
        }
        if (!isset($fiveLastWeeks[1][3])) {
            $fiveLastWeeks[1][3] = 0;
        }
        if (!isset($fiveLastWeeks[1][4])) {
            $fiveLastWeeks[1][4] = 0;
        }

        if (!isset($fiveLastWeeks[2][0])) {
            $fiveLastWeeks[2][0] = 0;
        }
        if (!isset($fiveLastWeeks[2][1])) {
            $fiveLastWeeks[2][1] = 0;
        }
        if (!isset($fiveLastWeeks[2][2])) {
            $fiveLastWeeks[2][2] = 0;
        }
        if (!isset($fiveLastWeeks[2][3])) {
            $fiveLastWeeks[2][3] = 0;
        }
        if (!isset($fiveLastWeeks[2][4])) {
            $fiveLastWeeks[2][4] = 0;
        }

        return $fiveLastWeeks;
    }
}
