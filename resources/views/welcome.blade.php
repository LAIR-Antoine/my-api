<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mes objectifs sportifs</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    @vite('resources/css/app.css')


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="antialiased">

    @if (Route::has('login'))
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            @auth
                <a href="{{ url('/dashboard') }}" class="dashboard-button">Dashboard</a>
            @else
                <a href="{{ route('login') }}"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Se connecter</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">S'inscrire</a>
                @endif
            @endauth
        </div>
    @endif


    <a class="refresh-button" href={{ route('refresh.goals') }}>Rafraîchir</a>
    <div class="goals-list" style="max-width: 1200px; margin-left: auto; margin-right: auto;">
        @foreach ($activeGoals as $goal)
            <div class="goal-card">
                @if ($goal->sport == 'swim')
                    <h3>Natation</h3>
                @elseif ($goal->sport == 'bike')
                    <h3>Vélo</h3>
                @elseif ($goal->sport == 'run')
                    <h3>Course à pied</h3>
                @endif

                <p class="date-goal">{{ date('d/m/Y', strtotime($goal->begin_date)) }} -
                    {{ date('d/m/Y', strtotime($goal->end_date)) }}</p>
                <p><span class="distance-done">{{ str_replace('.', ',', $goal->distance_done) }}</span> /
                    {{ $goal->distance_to_do }} km</p>
                @if ($goal->avancement != 0)
                    <div class="w3-container">
                        <div class="w3-light-grey w3-round-xlarge">
                            <div class="w3-container w3-blue w3-round-xlarge"
                                 style="width:{{ $goal->avancement <= 100 ? $goal->avancement : 100 }}%">
                                {{ $goal->avancement }}%
                            </div>
                        </div>
                    </div>
                    <p @if ($goal->gapGoal >= 0) class="green" @else class="red" @endif>
                        @if ($goal->gapGoal > 0)
                            +
                        @endif
                        {{ str_replace('.', ',', $goal->gapGoal) }} km
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="chartLine">
        <div>
            <h2>Semaine dernière</h2>
            <canvas id="sportTimeChart" width="400" height="200"></canvas>
        </div>
        <div>
            <h2>Semaine en cours</h2>
            <canvas id="sportTimeChart2" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="chartLine">
        <div>
            <h2>5 dernières semaines</h2>
            <canvas id="sportTimeChart3" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="chartLine">
        <div>
            <canvas id="sportTimeChart4" width="400" height="200"></canvas>
        </div>
        <div>
            <canvas id="sportTimeChart5" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="chartLine">
        <div>
            <canvas id="sportTimeChart6" width="400" height="200"></canvas>
        </div>
        <div>
            <canvas id="sportTimeChart7" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="chartLine">
        <div>
            <canvas id="sportTimeChart8" width="400" height="200"></canvas>
        </div>
        <div>
            <canvas id="sportTimeChart9" width="400" height="200"></canvas>
        </div>
    </div>

    <div class="eddingtonNumber">
        <p>Eddington number : {{ $eddingtonNumber }}</p>
        <p><small>Nombre de jour X où j'ai déjà fait plus de X kilomètres à vélo</small></p>
        <p>{{ $eddingtonNumberNextLevel }} sortie(s) de {{ $eddingtonNumber +1 }} km pour atteindre le palier {{ $eddingtonNumber + 1 }}</p>

    </div>

    <script>
        window.chartData = {
            pastWeekActivities: @json($pastWeekActivities),
            curWeekAct: @json($curWeekAct),
            fiveLastWeeks: @json($fiveLastWeeks),
            swimLastYear: @json($swimLastYear),
            swimThisYear: @json($swimThisYear),
            bikeLastYear: @json($bikeLastYear),
            bikeThisYear: @json($bikeThisYear),
            runLastYear: @json($runLastYear),
            runThisYear: @json($runThisYear)
        };
    </script>

    @vite('resources/js/home-chart.js')

</body>
</html>
