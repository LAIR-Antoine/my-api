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
                    <a class="button strava-button" href={{ route('strava.auth') }}>Se connecter à Strava</a> 
                    <br><br>
                    <a class="button blue-button" href={{ route('welcome') }}>Vue classique</a>
                    <br><br>
                     <a class="button strava-button" href={{ route('strava.sync') }}>Synchroniser les activitiés Strava</a> 
                    

                    <br><br>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
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
                                    //$time 
                                @endphp
                                <tr>
                                    <td>{{ date("d/m/Y", strtotime($activity->start_date_local)) }}</td>
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
</style>
