<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activities;
use IntlDateFormatter;

class StatsController extends Controller
{
    public function index()
    {
        $app = app();
        $yearStats = [];

        $firstActivity = Activities::orderBy('start_date_local', 'asc')->first();
        $lastActivity = Activities::orderBy('start_date_local', 'desc')->first();

        $firstYear = date('Y', strtotime($firstActivity->start_date_local));
        $lastYear = date('Y', strtotime($lastActivity->start_date_local));

        $year = $lastYear;

        for ($year; $firstYear <= $year; $year--) {
            $yearStats[$year] = $app->make('stdClass');
            $yearStats[$year]->year = $year;
            $yearStats[$year]->swim = round(Activities::where('type', 'Swim')
                ->whereYear('start_date_local', $year)
                ->sum('distance') / 1000, 2);
            $yearStats[$year]->bike = round((Activities::where('type', 'Ride')
                ->whereYear('start_date_local', $year)
                ->sum('distance') + Activities::where('type', 'VirtualRide')
                ->whereYear('start_date_local', $year)
                ->sum('distance')) / 1000, 2);
            $yearStats[$year]->run = round(Activities::where('type', 'Run')
                ->whereYear('start_date_local', $year)
                ->sum('distance') / 1000, 2);
        }

        return view('stats', compact('yearStats'));
    }

    public function monthStats($year)
    {

        $app = app();
        $monthStats = [];

        $firstMonth = 1;
        $lastMonth = 12;

        $month = $firstMonth;

        for ($month; $lastMonth >= $month; $month++) {
            $monthStats[$month] = $app->make('stdClass');

            $monthName = $this->getMonthName($month);
            $monthStats[$month]->month = $monthName;

            $monthStats[$month]->swim = round(Activities::where('type', 'Swim')
                ->whereYear('start_date_local', $year)
                ->whereMonth('start_date_local', $month)
                ->sum('distance') / 1000, 2);
            $monthStats[$month]->bike = round((Activities::where('type', 'Ride')
                ->whereYear('start_date_local', $year)
                ->whereMonth('start_date_local', $month)
                ->sum('distance') + Activities::where('type', 'VirtualRide')
                ->whereYear('start_date_local', $year)
                ->whereMonth('start_date_local', $month)
                ->sum('distance')) / 1000, 2);
            $monthStats[$month]->run = round(Activities::where('type', 'Run')
                ->whereYear('start_date_local', $year)
                ->whereMonth('start_date_local', $month)
                ->sum('distance') / 1000, 2);
        }

        return view('monthStats', compact('monthStats', 'year'));
    }

    public function getMonthName($monthNumber)
    {
        $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        $formatter->setPattern('MMMM');

        return ucfirst($formatter->format(mktime(0, 0, 0, $monthNumber)));
    }
}
