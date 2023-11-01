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
                    <a href={{ route('strava.auth') }}>Connect to Strava</a> | <a href={{ route('strava.sync') }}>Sync last Strava activities</a> | <a href={{ route('welcome') }}>Return to home</a>
                    | <a href={{ route('distance_goal') }}>CRUD goals</a>
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
                                    <td>{{ $activity->name }}</td>
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
    td {
        padding: 1rem;
        }
</style>
