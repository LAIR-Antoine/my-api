@section('title')
    Statistiques
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stats par semaine') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="centered-buttons">
                        <a class="button blue-button" href={{ route('stats.weeks', ['year' => $url['year'], 'week' => $url['week']-1]) }}>S {{ $url['week']-1 }}</a> 
                        <a class="button blue-button" href={{ route('stats.weeks', ['year' => $url['year'], 'week' => $url['week']+1]) }}>S {{ $url['week']+1 }}</a> 
                    </div>
                    <br />
                    <div class="chartLine">
                        <canvas id="sportTimeChart" width="400" height="200"></canvas>
                    </div>

                    <div class="weekReport">
                        @php
                            $hoursSwim = floor($weekStat['totalTimeSwim']);
                            $minutesSwim = round(($weekStat['totalTimeSwim'] - $hoursSwim) * 60);

                            $hoursBike = floor($weekStat['totalTimeBike']);
                            $minutesBike = round(($weekStat['totalTimeBike'] - $hoursBike) * 60);

                            $hoursRun = floor($weekStat['totalTimeRun']);
                            $minutesRun = round(($weekStat['totalTimeRun'] - $hoursRun) * 60);

                            $hoursTotal = floor($weekStat['totalTime']);
                            $minutesTotal = round(($weekStat['totalTime'] - $hoursTotal) * 60);
                        @endphp
                        <h2>S {{ $url['week'] }} - {{$weekStat['dates'][0]}} au {{$weekStat['dates'][6]}}</h2>
                        <p>Natation - <span class="swim">{{ str_replace('.', ',', round($weekStat['totalDistanceSwim'] /1000, 1)) }} km ({{ $hoursSwim }}h{{ str_pad($minutesSwim, 2, '0', STR_PAD_LEFT) }})</span></p>
                        <p>Vélo - <span class="bike">{{ str_replace('.', ',', round($weekStat['totalDistanceBike'] /1000, 1)) }} km ({{ $hoursBike }}h{{ str_pad($minutesBike, 2, '0', STR_PAD_LEFT) }})</span></p>
                        <p>Course - <span class="run">{{ str_replace('.', ',', round($weekStat['totalDistanceRun'] /1000, 1)) }} km ({{ $hoursRun }}h{{ str_pad($minutesRun, 2, '0', STR_PAD_LEFT) }})</span></p>
                        <h2>{{ $hoursTotal }}h{{ str_pad($minutesTotal, 2, '0', STR_PAD_LEFT) }}</h2>
                    </div>



                    <script>
                        var daysOfWeek = @json($weekStat['dates']);
                        var swim = @json($weekStat[0]);
                        var bike = @json($weekStat[1]);
                        var run = @json($weekStat[2]);
                
                        var sumPerDay = [];
                        for (let i = 0; i < swim.length; i++) {
                            sumPerDay.push(swim[i] + bike[i] + run[i]);
                        }
                        var maxDay = Math.ceil(Math.max(...sumPerDay));    

                        var ctx = document.getElementById('sportTimeChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: daysOfWeek,
                                datasets: [{
                                        label: 'Natation',
                                        data: swim,
                                        backgroundColor: 'rgba(69, 148, 209, 1)',
                                        stack: 'stack1' // Assign a stack name for Sport 1
                                    },
                                    {
                                        label: 'Vélo',
                                        data: bike,
                                        backgroundColor: 'rgba(100, 217, 208, 1)',
                                        stack: 'stack1' // Assign the same stack name for Sport 2
                                    },
                                    {
                                        label: 'Course',
                                        data: run,
                                        backgroundColor: 'rgba(255, 196, 0, 1)',
                                        stack: 'stack1' // Assign the same stack name for Sport 3
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true,
                                        ticks: {
                                            autoSkip: false, // Prevent automatic label skipping
                                            maxRotation: 0, // Rotate labels to 0 degrees (horizontal)
                                            minRotation: 0 // Rotate labels to 0 degrees (horizontal)
                                        }
                                    },
                                    y: {
                                        stacked: true,
                                        max: maxDay,
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1,
                                            callback: function(value) {
                                                return value + 'h';
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                        label: function(context) {
                                                let datasetLabel = context.dataset.label || '';
                                                let value = context.raw;
                                                let hours = Math.floor(value);
                                                let minutes = Math.floor((value - hours) * 60);
                                                let seconds = Math.round(((value - hours) * 60 - minutes) * 60);
                
                                                // Ensuring two digits for minutes and seconds
                                                minutes = ('0' + minutes).slice(-2);
                                                
                
                                                return datasetLabel + ': ' + `${hours}h${minutes}'`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    #sportTimeChart
    {
        max-width: 700px;
        max-height: 350px;
        margin: 10px auto 30px auto; 
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
    .centered-buttons {
        text-align: center;
    }
    .weekReport {
        text-align: center;
        color: gray; 
    }
    .weekReport h2 {
        font-size: 1.5em;

    }

    .weekReport span {
        font-size: 1.2em;
        font-weight: bold;
    }

    .weekReport .swim {
        color: rgba(69, 148, 209, 1);
    }
    .weekReport .bike {
        color: rgba(100, 217, 208, 1);
    }
    .weekReport .run {
        color: rgba(255, 196, 0, 1);
    }
</style>