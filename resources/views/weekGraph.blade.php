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
                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('stats') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour aux statistiques
                        </a>
                    </div>

                    <div class="centered-buttons">
                        <a class="button blue-button flex items-center justify-center inline-flex hover:bg-blue-600 transition-colors"
                           href={{ route('stats.weeks', ['year' => $url['year'], 'week' => $url['week']-1]) }}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Semaine {{ $url['week']-1 }}
                        </a>
                        <a class="button blue-button flex items-center justify-center inline-flex hover:bg-blue-600 transition-colors"
                           href={{ route('stats.weeks', ['year' => $url['year'], 'week' => $url['week']+1]) }}>
                            Semaine {{ $url['week']+1 }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

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

                        <h2 class="text-2xl font-bold mb-4">
                            Semaine {{ $url['week'] }} - {{$weekStat['dates'][0]}} au {{$weekStat['dates'][6]}}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto mb-6">
                            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                <p class="text-gray-600">Natation</p>
                                <p class="swim text-xl">
                                    {{ str_replace('.', ',', round($weekStat['totalDistanceSwim'] /1000, 1)) }} km
                                    <span class="text-sm block text-gray-500">
                                        {{ $hoursSwim }}h{{ str_pad($minutesSwim, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </p>
                            </div>

                            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                <p class="text-gray-600">Vélo</p>
                                <p class="bike text-xl">
                                    {{ str_replace('.', ',', round($weekStat['totalDistanceBike'] /1000, 1)) }} km
                                    <span class="text-sm block text-gray-500">
                                        {{ $hoursBike }}h{{ str_pad($minutesBike, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </p>
                            </div>

                            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                <p class="text-gray-600">Course</p>
                                <p class="run text-xl">
                                    {{ str_replace('.', ',', round($weekStat['totalDistanceRun'] /1000, 1)) }} km
                                    <span class="text-sm block text-gray-500">
                                        {{ $hoursRun }}h{{ str_pad($minutesRun, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 inline-block">
                            <p class="text-gray-600 text-sm">Total de la semaine</p>
                            <h2 class="text-2xl font-bold text-gray-800">
                                {{ $hoursTotal }}h{{ str_pad($minutesTotal, 2, '0', STR_PAD_LEFT) }}
                            </h2>
                        </div>
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
                                    stack: 'stack1'
                                },
                                    {
                                        label: 'Vélo',
                                        data: bike,
                                        backgroundColor: 'rgba(100, 217, 208, 1)',
                                        stack: 'stack1'
                                    },
                                    {
                                        label: 'Course',
                                        data: run,
                                        backgroundColor: 'rgba(255, 196, 0, 1)',
                                        stack: 'stack1'
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true,
                                        ticks: {
                                            autoSkip: false,
                                            maxRotation: 0,
                                            minRotation: 0
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
    #sportTimeChart {
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

    .blue-button {
        background-color: rgb(69, 148, 209);
    }

    .centered-buttons {
        text-align: center;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .weekReport {
        text-align: center;
        color: gray;
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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .centered-buttons {
            flex-direction: column;
            align-items: center;
        }

        .button {
            width: 100%;
            max-width: 300px;
        }
    }
</style>
