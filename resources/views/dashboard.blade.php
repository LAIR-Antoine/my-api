<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const queryString = window.location.search;
                            const urlParams = new URLSearchParams(queryString);
                            const state = urlParams.get('state');
                            if (state == 'strava') {
                                window.location.href = "/dashboard";
                            }
                        });
                    </script>
                    @if (!$user->strava_access_token)
                        <a class="button strava-button" href={{ route('strava.auth') }}>Se connecter à Strava</a> 
                        <br><br>
                    @else
                        <a class="button strava-button" href={{ route('strava.sync') }}>Synchroniser les activitiés Strava</a> 
                        <br><br>
                    @endif
                    <a class="button blue-button" href={{ route('stats.weeks', ['year' => 2023, 'week' => 46]) }}>Stats semaine</a> 
                    <br>

                    <br><br>
                    <h2>Prochaines séances</h2>
                    <ul class="calendarBlock">
                        {{-- <li><p>Lundi</p></li>
                        <li><p>Mardi</p></li>
                        <li><p>Mercredi</p></li>
                        <li><p>Jeudi</p></li>
                        <li><p>Vendredi</p></li>
                        <li><p>Samedi</p></li>
                        <li><p>Dimanche</p></li> --}}
                        @foreach ($calendar as $day)
                            <li class="calendarCard {{ $day['type'] }}Card">
                                <p>{{ $day['date'] }}</p>
                                <p class="calendarTime">{{ $day['duration'] }}</p>
                                <p class="calendarTitle">{{ $day['name'] }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <br><br>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sport</th>
                                <th>Activités</th>
                                <th>Distance</th>
                                <th>Temps</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                @php
                                    $hours = floor($activity->moving_time / 3600);
                                    $minutes = floor(($activity->moving_time / 60) % 60);
                                    if ($minutes < 10) {
                                        $minutes = '0' . $minutes;
                                    }
                                    $seconds = $activity->moving_time % 60;
                                    if ($seconds < 10) {
                                        $seconds = '0' . $seconds;
                                    }
                                    
                                    $time = ($hours != 0 ? $hours . 'h' : null) . $minutes . '\'' . $seconds . '"';
                                @endphp
                                <tr>
                                    <td>{{ date("d/m/Y", strtotime($activity->start_date_local)) }}</td>
                                    <td class="sportEmote">@if($activity->type == 'Swim') 🏊🏻‍♂️ @elseif($activity->type == 'Ride' || $activity->type == 'VirtualRide') 🚴🏻‍♂️ @elseif($activity->type == 'Run') 🏃🏻‍♂️ @endif</td>
                                    <td><a href={{ 'https://www.strava.com/activities/' . $activity->strava_id }} target="_blank">{{ $activity->name }}</a></td>
                                    <td>{{ str_replace('.', ',', round($activity->distance / 1000, 2)) }} km</td>
                                    <td>{{ $time }}</td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    td, th {
        padding: 1rem;
        border: 1px solid lightgrey;
        border-collapse: collapse;
        }
    
    .button {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        color: white;
        margin: 20px 10px;
        font-weight: bold;  
    }
    .strava-button {
        background-color: rgb(252,82,0);
        
    }
    .blue-button {
        background-color: rgb(69, 148, 209);  
    }
    .sportEmote {
        font-size: 2rem;
    }
    .SwimCard {
        background-color: rgba(69, 148, 209, 1);
    }
    .RideCard {
        background-color: rgba(100, 217, 208, 1);
    }
    .RunCard {
        background-color: rgba(255, 196, 0, 1);
    }
    .calendarCard {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        color: black;
        margin: 20px 10px;
        font-weight: bold;  

        display: flex;
        flex-direction: row;
        
    }
    .calendarCard p {
        margin: 0;
        padding: 10px 10px;
    }
    .calendarBlock {
        width: fit-content;
        margin: 0 auto;
    }
    h2 {
        font-size: 1.5rem;
        color: rgba(69, 148, 209, 1);
        padding: 0 10px;
        margin: 0 auto;
        font-weight: bold;
        text-align: center;
    }
/*     .calendarBlock {
        display: flex;
        flex-wrap: wrap;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
    }
    .calendarTime {
        font-size: 1.2rem;
    }
    .calendarTitle {
        font-size: 0.8rem;
    } */
</style>
