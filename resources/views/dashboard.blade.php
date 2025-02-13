@section('title')
    Accueil
@endsection

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
                    <a class="button blue-button" href={{ route('stats.weeks', [
                        'year' => now()->year,
                        'week' => now()->week
                    ]) }}>Stats semaine</a>
                    <br>

                    <br><br>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activités</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temps</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            function formatActivityTime($seconds) {
                                $hours = floor($seconds / 3600);
                                $minutes = floor(($seconds / 60) % 60);
                                $secs = $seconds % 60;

                                $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                $secs = str_pad($secs, 2, '0', STR_PAD_LEFT);

                                return ($hours > 0 ? $hours . 'h' : '') . $minutes . '\'' . $secs . '"';
                            }
                        @endphp

                        @foreach ($activities as $activity)
                            @php
                                $movingTime = formatActivityTime($activity->moving_time);
                                $elapsedTime = formatActivityTime($activity->elapsed_time);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ date("d/m/Y", strtotime($activity->start_date_local)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap sportEmote">
                                    @if($activity->type == 'Swim')
                                        🏊🏻‍♂️
                                    @elseif($activity->type == 'Ride' || $activity->type == 'VirtualRide')
                                        🚴🏻‍♂️
                                    @elseif($activity->type == 'Run')
                                        🏃🏻‍♂️
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ 'https://www.strava.com/activities/' . $activity->strava_id }}"
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        {{ $activity->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ str_replace('.', ',', round($activity->distance / 1000, 2)) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->type == 'Swim')
                                        <div class="flex items-center space-x-2">
                            <span class="time-display" data-moving="{{ $movingTime }}" data-elapsed="{{ $elapsedTime }}">
                                {{ $movingTime }}
                            </span>
                                            <form action="{{ route('activities.update', $activity->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="use_elapsed_time" value="{{ $activity->moving_time === $activity->elapsed_time ? '0' : '1' }}">
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                        title="Basculer entre temps en mouvement et temps écoulé">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @if($activity->moving_time !== $activity->elapsed_time)
                                                <span class="text-gray-400 text-xs">
                                    ({{ $activity->moving_time === $activity->elapsed_time ? $movingTime : $elapsedTime }})
                                </span>
                                            @endif
                                        </div>
                                    @else
                                        {{ $movingTime }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.querySelectorAll('.time-switch').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const display = this.previousElementSibling;
            const moving = display.dataset.moving;
            const elapsed = display.dataset.elapsed;
            const isMoving = display.textContent.trim() === moving;

            display.textContent = isMoving ? elapsed : moving;
        });
    });
</script>

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
    h2 {
        font-size: 1.5rem;
        color: rgba(69, 148, 209, 1);
        padding: 0 10px;
        margin: 0 auto;
        font-weight: bold;
        text-align: center;
    }
    .time-display {
        min-width: 70px;
        display: inline-block;
    }
</style>
