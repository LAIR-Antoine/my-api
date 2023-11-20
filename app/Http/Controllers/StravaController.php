<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Strava;
use App\Models\User;
use App\Models\Activities;
use App\Models\DistanceGoal;


class StravaController extends Controller
{
    public function dashboard(Request $request)
    {
        

        $user = User::find(auth()->id());
        $activities = Activities::orderBy('start_date_local', 'desc')->paginate(15);

        if ($request->code && !$user->strava_access_token) {
            $this->getToken($request);
        }

        return view('dashboard', compact('activities'));
    }

    public function stravaAuth()
    {
        $user = User::find(auth()->id());
        $user->strava_access_token = null;
        $user->strava_refresh_token = null;
        $user->save();
        return Strava::authenticate($scope='read_all,profile:read_all,activity:read_all');
    }

    public function getToken(Request $request)
    {
        //dd($request->code);
        //dd(auth()->id());
        $token = Strava::token($request->code);
        $user = User::find(auth()->id());
        $user->strava_access_token = $token->access_token;
        $user->strava_refresh_token = $token->refresh_token;
        $user->save();

        return redirect()->route('dashboard');

        //dd($token);
        // Store $token->access_token & $token->refresh_token in database
    }

    public function refreshTokenIfNeeded($user)
    {
        // Check if the current time is past the token's expiry time
        if (now()->greaterThan($user->token_expires_at)) {
            // Refresh the token
            $this->refreshToken($user);
        }
    }

    private function refreshToken($user)
    {
        try {
            // Use Strava's token refresh endpoint to get a new token
            $response = Strava::refreshToken($user->strava_refresh_token);

            // Update user's tokens and expiry time in the database
            $user->update([
                'strava_access_token' => $response->access_token,
                'strava_refresh_token' => $response->refresh_token,
                'token_expires_at' => now()->addSeconds($response->expires_in),
            ]);
        } catch (\Exception $e) {
            // Handle exceptions, e.g., logging
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
            //dd($activities);
            foreach ($activities as $activity) {
                $activityExist = Activities::where('strava_id', $activity->id)->first();
                if ($activityExist) {
    
                } else {
                    $location = ($activity->location_city ? $activity->location_city . ', ' : null) . ($activity->location_state ? $activity->location_state . ', ' : null) . ($activity->location_country ? $activity->location_country : null); 
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
        }

        return redirect()->route('dashboard');
        
    }

    public function syncActivities() 
    {
        $user = User::find(auth()->id());
        $this->refreshTokenIfNeeded($user);
        
        $page = 1;

       
        $activities = Strava::activities($user->strava_access_token, $page, 20); //max 200
        
        
        //dd($activities);
        foreach ($activities as $activity) {
            $activityExist = Activities::where('strava_id', $activity->id)->first();
            if ($activityExist) {

            } else {
                $location = ($activity->location_city ? $activity->location_city . ', ' : null) . ($activity->location_state ? $activity->location_state . ', ' : null) . ($activity->location_country ? $activity->location_country : null); 
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

    public function refreshGoals() {
        $goals = DistanceGoal::all();

        foreach ($goals as $goal) {
            $activities = Activities::where('start_date_local', '>=', $goal->begin_date)->where('start_date_local', '<=', $goal->end_date)->get();

            $goal->distance_done = 0;
            foreach ($activities as $activity) {
                //$goal->distance_done += $activity->distance;
                if (($activity->type == 'Ride' || $activity->type == 'VirtualRide') && $goal->sport == 'bike') {
                    $goal->distance_done += $activity->distance;
                } else if (($activity->type == 'Run') && $goal->sport == 'run') {
                    $goal->distance_done += $activity->distance;
                } else if (($activity->type == 'Swim') && $goal->sport == 'swim') {
                    $goal->distance_done += $activity->distance;
                }
            }
            $goal->distance_done = $goal->distance_done / 1000;
            $goal->save();
            //dd($activities);
        }

        return redirect()->route('welcome');
    }
}
