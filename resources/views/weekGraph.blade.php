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
</style>