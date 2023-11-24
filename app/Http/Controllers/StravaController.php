<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Strava;
use App\Models\User;
use App\Models\Activities;
use App\Models\DistanceGoal;
use GuzzleHttp\Client;
use ICal\ICal;
use Carbon\Carbon;
use Config;

class StravaController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = User::find(auth()->id());
        $activities = Activities::orderBy('start_date_local', 'desc')->paginate(15);

        if ($request->code && !$user->strava_access_token) {
            $this->getToken($request);
        }

        $googleCalendar = config('calendar.url');
        $client = new Client();
        $response = $client->get($googleCalendar);
        $icalFile = $response->getBody()->getContents();


        try {
            $ical = new ICal($icalFile, array(
                'defaultSpan'                 => 2,     // Default value
                'defaultTimeZone'             => 'UTC',
                'defaultWeekStart'            => 'MO',  // Default value
                'disableCharacterReplacement' => false, // Default value
                'filterDaysAfter'             => null,  // Default value
                'filterDaysBefore'            => null,  // Default value
                'httpUserAgent'               => null,  // Default value
                'skipRecurrence'              => false, // Default value
            ));
            //dd($ical);
            $today = Carbon::today();

// Get the date 10 days from today
            $tenDaysLater = $today->copy()->addDays(15);

// Call the eventsFromRange method with dynamic dates
            $actualEvents = $ical->eventsFromRange($today->toDateString(), $tenDaysLater->toDateString());
            //dd($actualEvents);
            $calendar = $this->extractIcsData($actualEvents);
        } catch (\Exception $e) {
            die($e);
        }


        return view('dashboard', compact('activities', 'user', 'calendar'));
    }

    public function extractIcsData($events)
    {
        $formattedEvents = [];
        foreach ($events as $index => $event) {
            $description = $event->description;
            if (str_contains($event->summary, '[Note]') || str_contains($event->summary, '[Cycle]')) {
                continue;
            } else {
                $formattedEvents[$index]['name'] = preg_replace('/[\x🏊🚴🏃♂️]/u', '', $event->summary);
                $formattedEvents[$index]['date'] = $this->convertIcsDate($event->dtstart);
                if (str_contains($event->summary, '🏃')) {
                    $formattedEvents[$index]['type'] = 'Run';
                } elseif (str_contains($event->summary, '🏊')) {
                    $formattedEvents[$index]['type'] = 'Swim';
                } elseif (str_contains($event->summary, '🚴')) {
                    $formattedEvents[$index]['type'] = 'Ride';
                } else {
                    $formattedEvents[$index]['type'] = 'Other';
                }

                if (preg_match('/Durée\s*:\s*(\d{1,2}:\d{2}:\d{2})/', $description, $matches)) {
                    $formattedEvents[$index]['duration'] = $this->convertTime($matches[1]);
                }
            }
        }

        return $formattedEvents;
    }

    public function convertIcsDate($date)
    {
        // yyyymmdd to dd/mm/yyyy
        $date = substr($date, 6, 2) . '/' . substr($date, 4, 2) . '/' . substr($date, 0, 4);

        return $date;
    }

    public function convertTime($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);

        // Removing leading zeros
        $hours = ltrim($hours, '0');
        $minutes = ltrim($minutes, '0');
        $seconds = ltrim($seconds, '0');

        // Adding default values if any part is empty
        if (empty($hours)) {
            $hours = '0';
        }
        if (empty($minutes)) {
            $minutes = '0';
        }
        if (empty($seconds)) {
            $seconds = '0';
        }

        if (strlen($minutes) == 1) {
            $minutes = '0' . $minutes;
        }
        if (strlen($seconds) == 1) {
            $seconds = '0' . $seconds;
        }

        if ($hours == 0) {
            $total = $minutes . "'" . $seconds . "''";
        } else {
            $total = $hours . 'h' . $minutes . "'" . $seconds . "''";
        }

        return $total;
    }

    public function stravaAuth()
    {
        $user = User::find(auth()->id());
        $user->strava_access_token = null;
        $user->strava_refresh_token = null;
        $user->save();
        return Strava::authenticate($scope = 'read_all,profile:read_all,activity:read_all');
    }

    public function getToken(Request $request)
    {
        $token = Strava::token($request->code);
        $user = User::find(auth()->id());
        $user->strava_access_token = $token->access_token;
        $user->strava_refresh_token = $token->refresh_token;
        $user->token_expires_at = now()->addSeconds($token->expires_in);
        $user->save();

        return redirect()->route('dashboard');
    }

    public function refreshTokenIfNeeded($user)
    {
        if (now()->greaterThan($user->token_expires_at)) {
            $this->refreshToken($user);
        }
    }

    private function refreshToken($user)
    {
        try {
            $response = Strava::refreshToken($user->strava_refresh_token);
            $user->update([
                'strava_access_token' => $response->access_token,
                'strava_refresh_token' => $response->refresh_token,
                'token_expires_at' => now()->addSeconds($response->expires_in)
            ]);
            $user->save();
        } catch (\Exception $e) {
            Log::error("Error refreshing Strava token: " . $e->getMessage());
        }
    }

    public function syncAllActivities()
    {
        $user = User::find(auth()->id());

        $this->refreshTokenIfNeeded($user);
        $allSynced = false;
        $page = 1;

        while ($allSynced == false) {
            $activities = Strava::activities($user->strava_access_token, $page, 200); //max 200
            $page++;
            if (count($activities) == 0) {
                $allSynced = true;
            }
            foreach ($activities as $activity) {
                $activityExist = Activities::where('strava_id', $activity->id)->first();
                if ($activityExist) {
                    $activitiyToUpdate = Activities::where('strava_id', $activity->id)->first();
                    $activitiyToUpdate->name = $activity->name;
                    $activitiyToUpdate->save();
                } else {
                    $city = $activity->location_city ? $activity->location_city . ', ' : null;
                    $state = $activity->location_state ? $activity->location_state . ', ' : null;
                    $country = $activity->location_country ? $activity->location_country : null;
                    $location = $city . $state . $country;
                    $elevation = $activity->total_elevation_gain ? $activity->total_elevation_gain : null;
                    $heatrate = isset($activity->average_heartrate) ? $activity->average_heartrate : null;
                    $activitiyToCreate = Activities::create([
                        'strava_id' => $activity->id,
                        'name' => $activity->name,
                        'type' => $activity->type,
                        'start_date_local' => date('Y-m-d h:i:s', strtotime($activity->start_date_local)),
                        'location' => $location,
                        'distance' => $activity->distance ? $activity->distance : null,
                        'moving_time' => $activity->moving_time ? $activity->moving_time : null,
                        'elapsed_time' => $activity->elapsed_time ? $activity->elapsed_time : null,
                        'total_elevation_gain' => $elevation,
                        'average_speed' => $activity->average_speed ? $activity->average_speed : null,
                        'max_speed' => $activity->max_speed ? $activity->max_speed : null,
                        'average_heartrate' => $heatrate,
                        'max_heartrate' => isset($activity->max_heartrate) ? $activity->max_heartrate : null,
                        'average_cadence' => isset($activity->average_cadence) ? $activity->average_cadence : null,
                        'average_watts' => isset($activity->average_watts) ? $activity->average_watts : null,
                        'max_watts' => isset($activity->max_watts) ? $activity->max_watts : null,
                        'suffer_score' => isset($activity->suffer_score) ? $activity->suffer_score : null,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard');
    }

    public function syncActivities()
    {
        $user = User::find(auth()->id());
        $this->refreshTokenIfNeeded($user);

        $page = 1;

        $activities = Strava::activities($user->strava_access_token, $page, 20); //max 200

        foreach ($activities as $activity) {
            $activityExist = Activities::where('strava_id', $activity->id)->first();
            if ($activityExist) {
                $activitiyToUpdate = Activities::where('strava_id', $activity->id)->first();
                $activitiyToUpdate->name = $activity->name;
                $activitiyToUpdate->save();
            } else {
                $city = $activity->location_city ? $activity->location_city . ', ' : null;
                $state = $activity->location_state ? $activity->location_state . ', ' : null;
                $country = $activity->location_country ? $activity->location_country : null;
                $location = $city . $state . $country;
                $activitiyToCreate = Activities::create([
                    'strava_id' => $activity->id,
                    'name' => $activity->name,
                    'type' => $activity->type,
                    'start_date_local' => date('Y-m-d h:i:s', strtotime($activity->start_date_local)),
                    'location' => $location,
                    'distance' => $activity->distance ? $activity->distance : null,
                    'moving_time' => $activity->moving_time ? $activity->moving_time : null,
                    'elapsed_time' => $activity->elapsed_time ? $activity->elapsed_time : null,
                    'total_elevation_gain' => $activity->total_elevation_gain ? $activity->total_elevation_gain : null,
                    'average_speed' => $activity->average_speed ? $activity->average_speed : null,
                    'max_speed' => $activity->max_speed ? $activity->max_speed : null,
                    'average_heartrate' => isset($activity->average_heartrate) ? $activity->average_heartrate : null,
                    'max_heartrate' => isset($activity->max_heartrate) ? $activity->max_heartrate : null,
                    'average_cadence' => isset($activity->average_cadence) ? $activity->average_cadence : null,
                    'average_watts' => isset($activity->average_watts) ? $activity->average_watts : null,
                    'max_watts' => isset($activity->max_watts) ? $activity->max_watts : null,
                    'suffer_score' => isset($activity->suffer_score) ? $activity->suffer_score : null,
                ]);
            }
        }

        return redirect()->route('dashboard');
    }

    public function refreshGoals()
    {
        $goals = DistanceGoal::all();

        foreach ($goals as $goal) {
            $activities = Activities::where('start_date_local', '>=', $goal->begin_date)
                ->where('start_date_local', '<=', $goal->end_date)
                ->get();

            $goal->distance_done = 0;
            foreach ($activities as $activity) {
                if (($activity->type == 'Ride' || $activity->type == 'VirtualRide') && $goal->sport == 'bike') {
                    $goal->distance_done += $activity->distance;
                } elseif (($activity->type == 'Run') && $goal->sport == 'run') {
                    $goal->distance_done += $activity->distance;
                } elseif (($activity->type == 'Swim') && $goal->sport == 'swim') {
                    $goal->distance_done += $activity->distance;
                }
            }
            $goal->distance_done = $goal->distance_done / 1000;
            $goal->save();
        }

        return redirect()->route('welcome');
    }
}
