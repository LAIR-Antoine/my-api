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

    public function show(DistanceGoal $distanceGoal)
    {
        return view('distance_goal', compact('distance_goal'));
    }

    public function edit(DistanceGoal $distanceGoal)
    {
        return view('distance_goal.edit', compact('distance_goal'));
    }

    public function update(Request $request, DistanceGoal $distanceGoal)
    {
        $request->validate([
            'sport' => 'required',
            'distance_to_do' => 'required',
            'distance_done' => 'required',
            'begin_date' => 'required',
            'end_date' => 'required',
            'state' => 'required',
        ]);
        $distanceGoal->update($request->all());

        return redirect()->route('distance_goal')
            ->with('success', 'DistanceGoal updated successfully');
    }

    public function destroy(DistanceGoal $distanceGoal)
    {
        $distanceGoal->delete();

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
            } elseif ($today > $goal->end_date) {
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

        $curWeekAct = [];
        $pastWeekActivities = [];

        $weekDaysLetter = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

        foreach ($currentWeekDates as $index => $date) {
            $day = date('d', strtotime($date));
            $month = date('m', strtotime($date));
            $curWeekAct['dates'][$index] = $weekDaysLetter[$index] . ' ' . $day . '/' . $month;

            $curWeekActDB = Activities::where('start_date_local', '>=', $date . ' 00:00:00')
                ->where('start_date_local', '<=', $date . ' 23:59:59')
                ->get();
            foreach ($curWeekActDB as $activity) {
                if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                    $curWeekAct[1][$index] = $activity->moving_time / 3600;
                } elseif ($activity->type == 'Run') {
                    $curWeekAct[2][$index] = $activity->moving_time / 3600;
                } elseif ($activity->type == 'Swim') {
                    $curWeekAct[0][$index] = $activity->moving_time / 3600;
                }
            }
            if (!isset($curWeekAct[0][$index])) {
                $curWeekAct[0][$index] = 0;
            }
            if (!isset($curWeekAct[1][$index])) {
                $curWeekAct[1][$index] = 0;
            }
            if (!isset($curWeekAct[2][$index])) {
                $curWeekAct[2][$index] = 0;
            }
        }

        foreach ($pastWeekDates as $index => $date) {
            $day = date('d', strtotime($date));
            $month = date('m', strtotime($date));
            $pastWeekActivities['dates'][$index] = $weekDaysLetter[$index] . ' ' . $day . '/' . $month;
            $pastWeekActivitiesDB = Activities::where('start_date_local', '>=', $date . ' 00:00:00')
                ->where('start_date_local', '<=', $date . ' 23:59:59')
                ->get();
            foreach ($pastWeekActivitiesDB as $activity) {
                if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                    $pastWeekActivities[1][$index] = $activity->moving_time / 3600;
                } elseif ($activity->type == 'Run') {
                    $pastWeekActivities[2][$index] = $activity->moving_time / 3600;
                } elseif ($activity->type == 'Swim') {
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

        //dd($curWeekAct, $pastWeekActivities);

        $fiveLastWeeks = $this->getFiveLastWeeksForChart();

        $swimLastYear = $this->getYearDistancePerMonthPerSport('2022', 'Swim');
        $swimThisYear = $this->getYearDistancePerMonthPerSport('2023', 'Swim');

        $bikeLastYear = $this->getYearDistancePerMonthPerSport('2022', 'Ride');
        $bikeThisYear = $this->getYearDistancePerMonthPerSport('2023', 'Ride');

        $runLastYear = $this->getYearDistancePerMonthPerSport('2022', 'Run');
        $runThisYear = $this->getYearDistancePerMonthPerSport('2023', 'Run');

        return view('welcome', compact(
            'activeGoals',
            'curWeekAct',
            'pastWeekActivities',
            'fiveLastWeeks',
            'swimLastYear',
            'swimThisYear',
            'bikeLastYear',
            'bikeThisYear',
            'runLastYear',
            'runThisYear'
        ));
    }

    public function getYearDistancePerMonthPerSport($year, $sport)
    {
        $activities = Activities::where('start_date_local', '>=', $year . '-01-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-12-31 23:59:59')
            ->where('type', 'LIKE', '%' . $sport . '%')
            ->get();

        $yearDistancePerMonth = array();

        $yearDistancePerMonth[1] = round($activities->where('start_date_local', '>=', $year . '-01-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-01-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[2] = round($activities->where('start_date_local', '>=', $year . '-02-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-02-29 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[3] = round($activities->where('start_date_local', '>=', $year . '-03-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-03-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[4] = round($activities->where('start_date_local', '>=', $year . '-04-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-04-30 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[5] = round($activities->where('start_date_local', '>=', $year . '-05-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-05-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[6] = round($activities->where('start_date_local', '>=', $year . '-06-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-06-30 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[7] = round($activities->where('start_date_local', '>=', $year . '-07-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-07-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[8] = round($activities->where('start_date_local', '>=', $year . '-08-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-08-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[9] = round($activities->where('start_date_local', '>=', $year . '-09-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-09-30 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[10] = round($activities->where('start_date_local', '>=', $year . '-10-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-10-31 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[11] = round($activities->where('start_date_local', '>=', $year . '-11-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-11-30 23:59:59')
            ->sum('distance') / 1000, 1);

        $yearDistancePerMonth[12] = round($activities->where('start_date_local', '>=', $year . '-12-01 00:00:00')
            ->where('start_date_local', '<=', $year . '-12-31 23:59:59')
            ->sum('distance') / 1000, 1);

        foreach ($yearDistancePerMonth as $key => $value) {
            if ($value == null) {
                $yearDistancePerMonth[$key] = 0;
            }
        }

        return $yearDistancePerMonth;
    }

    public function getFiveLastWeeksForChart()
    {

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

        $fiveLastWeeks = [];
        $fiveLastWeeks['dates'] = [
            'S ' . $pastFourWeekNumber,
            'S ' . $pastThreeWeekNumber,
            'S ' . $pastTwoWeekNumber,
            'S ' . $pastOneWeekNumber,
            'S ' . $currentWeekNumber
        ];

        $pastFourWeekNumAct = Activities::where('start_date_local', '>=', $pastFourStart . ' 00:00:00')
            ->where('start_date_local', '<=', $pastFourEnd . ' 23:59:59')
            ->get();
        $pastThreeWeekNumAct = Activities::where('start_date_local', '>=', $pastThreeStart . ' 00:00:00')
            ->where('start_date_local', '<=', $pastThreeEnd . ' 23:59:59')
            ->get();
        $pastTwoWeekNumAct = Activities::where('start_date_local', '>=', $pastTwoStart . ' 00:00:00')
            ->where('start_date_local', '<=', $pastTwoEnd . ' 23:59:59')
            ->get();
        $pastOneWeekNumAct = Activities::where('start_date_local', '>=', $pastOneStart . ' 00:00:00')
            ->where('start_date_local', '<=', $pastOneEnd . ' 23:59:59')
            ->get();
        $curWeekNumAct = Activities::where('start_date_local', '>=', $currentWeekStart . ' 00:00:00')
            ->where('start_date_local', '<=', $currentWeekEnd . ' 23:59:59')
            ->get();

        foreach ($pastFourWeekNumAct as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][0] = (isset($fiveLastWeeks[1][0]) ? $fiveLastWeeks[1][0] : null);
                $fiveLastWeeks[1][0] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Run') {
                $fiveLastWeeks[2][0] =  (isset($fiveLastWeeks[2][0]) ? $fiveLastWeeks[2][0] : null);
                $fiveLastWeeks[2][0] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Swim') {
                $fiveLastWeeks[0][0] =  (isset($fiveLastWeeks[0][0]) ? $fiveLastWeeks[0][0] : null);
                $fiveLastWeeks[0][0] += ($activity->moving_time / 3600);
            }
        }

        foreach ($pastThreeWeekNumAct as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][1] = (isset($fiveLastWeeks[1][1]) ? $fiveLastWeeks[1][1] : null);
                $fiveLastWeeks[1][1] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Run') {
                $fiveLastWeeks[2][1] = (isset($fiveLastWeeks[2][1]) ? $fiveLastWeeks[2][1] : null);
                $fiveLastWeeks[2][1] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Swim') {
                $fiveLastWeeks[0][1] = (isset($fiveLastWeeks[0][1]) ? $fiveLastWeeks[0][1] : null);
                $fiveLastWeeks[0][1] += ($activity->moving_time / 3600);
            }
        }

        foreach ($pastTwoWeekNumAct as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][2] = (isset($fiveLastWeeks[1][2]) ? $fiveLastWeeks[1][2] : null);
                $fiveLastWeeks[1][2] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Run') {
                $fiveLastWeeks[2][2] = (isset($fiveLastWeeks[2][2]) ? $fiveLastWeeks[2][2] : null);
                $fiveLastWeeks[2][2] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Swim') {
                $fiveLastWeeks[0][2] = (isset($fiveLastWeeks[0][2]) ? $fiveLastWeeks[0][2] : null);
                $fiveLastWeeks[0][2] += ($activity->moving_time / 3600);
            }
        }

        foreach ($pastOneWeekNumAct as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][3] = (isset($fiveLastWeeks[1][3]) ? $fiveLastWeeks[1][3] : null);
                $fiveLastWeeks[1][3] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Run') {
                $fiveLastWeeks[2][3] = (isset($fiveLastWeeks[2][3]) ? $fiveLastWeeks[2][3] : null);
                $fiveLastWeeks[2][3] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Swim') {
                $fiveLastWeeks[0][3] = (isset($fiveLastWeeks[0][3]) ? $fiveLastWeeks[0][3] : null);
                $fiveLastWeeks[0][3] += ($activity->moving_time / 3600);
            }
        }

        foreach ($curWeekNumAct as $activity) {
            if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                $fiveLastWeeks[1][4] = (isset($fiveLastWeeks[1][4]) ? $fiveLastWeeks[1][4] : null);
                $fiveLastWeeks[1][4] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Run') {
                $fiveLastWeeks[2][4] = (isset($fiveLastWeeks[2][4]) ? $fiveLastWeeks[2][4] : null);
                $fiveLastWeeks[2][4] += ($activity->moving_time / 3600);
            } elseif ($activity->type == 'Swim') {
                $fiveLastWeeks[0][4] = (isset($fiveLastWeeks[0][4]) ? $fiveLastWeeks[0][4] : null);
                $fiveLastWeeks[0][4] += ($activity->moving_time / 3600);
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

    public function getWeekStat($year, $week)
    {
        $weekStart = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $weekEnd = Carbon::now()->setISODate($year, $week)->endOfWeek();

        $weekDates = [];

        for ($date = $weekStart; $date <= $weekEnd; $date->addDay()) {
            $weekDates[] = $date->toDateString();
        }

        $weekStat = [];
        $weekStat['totalDistanceSwim'] = 0;
        $weekStat['totalDistanceBike'] = 0;
        $weekStat['totalDistanceRun'] = 0;


        $weekDaysLetter = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

        foreach ($weekDates as $index => $date) {
            $day = date('d', strtotime($date));
            $month = date('m', strtotime($date));
            $weekStat['dates'][$index] = $weekDaysLetter[$index] . ' ' . $day . '/' . $month;

            $weekStatDB = Activities::where('start_date_local', '>=', $date . ' 00:00:00')
                ->where('start_date_local', '<=', $date . ' 23:59:59')
                ->get();

            foreach ($weekStatDB as $activity) {
                if ($activity->type == 'Ride' || $activity->type == 'VirtualRide') {
                    $weekStat[1][$index] = $activity->moving_time / 3600;
                    $weekStat['totalDistanceBike'] += $activity->distance;
                } elseif ($activity->type == 'Run') {
                    $weekStat[2][$index] = $activity->moving_time / 3600;
                    $weekStat['totalDistanceRun'] += $activity->distance;
                } elseif ($activity->type == 'Swim') {
                    $weekStat[0][$index] = $activity->moving_time / 3600;
                    $weekStat['totalDistanceSwim'] += $activity->distance;
                }
            }

            if (!isset($weekStat[0][$index])) {
                $weekStat[0][$index] = 0;
            }
            if (!isset($weekStat[1][$index])) {
                $weekStat[1][$index] = 0;
            }
            if (!isset($weekStat[2][$index])) {
                $weekStat[2][$index] = 0;
            }
        }
        $weekStat['totalTimeSwim'] = array_sum($weekStat[0]);
        $weekStat['totalTimeBike'] = array_sum($weekStat[1]);
        $weekStat['totalTimeRun'] = array_sum($weekStat[2]);
        $weekStat['totalTime'] = $weekStat['totalTimeSwim'] + $weekStat['totalTimeBike'] + $weekStat['totalTimeRun'];


        //dd($weekStat);
        return $weekStat;
    }

    public function weekStat(Request $request)
    {
        $weekStat = $this->getWeekStat($request->year, $request->week);
        $url = ['year' => $request->year, 'week' => $request->week];

        return view('weekGraph', compact('weekStat', 'url'));
    }
}
