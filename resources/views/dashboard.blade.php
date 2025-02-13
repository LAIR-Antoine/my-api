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

                                return $hours . 'h' . $minutes . '\'' . $secs . '"';
                            }
                        @endphp

                        @foreach ($activities as $activity)
                            @php
                                $movingHours = floor($activity->moving_time / 3600);
                                $movingMinutes = floor(($activity->moving_time / 60) % 60);
                                $movingSeconds = $activity->moving_time % 60;

                                $elapsedHours = floor($activity->elapsed_time / 3600);
                                $elapsedMinutes = floor(($activity->elapsed_time / 60) % 60);
                                $elapsedSeconds = $activity->elapsed_time % 60;

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
                                            <form action="{{ route('activities.update', $activity->id) }}"
                                                  method="POST"
                                                  class="inline-flex items-center space-x-2"
                                                  onsubmit="return validateTime(this)">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex items-center space-x-1">
                                                    <input type="number"
                                                           name="hours"
                                                           value="{{ $movingHours }}"
                                                           class="time-input w-12 px-2 py-1 border border-gray-300 rounded-md text-sm"
                                                           min="0"
                                                           max="23">
                                                    <span>h</span>
                                                    <input type="number"
                                                           name="minutes"
                                                           value="{{ str_pad($movingMinutes, 2, '0', STR_PAD_LEFT) }}"
                                                           class="time-input w-14 px-2 py-1 border border-gray-300 rounded-md text-sm"
                                                           min="0"
                                                           max="59">
                                                    <span>'</span>
                                                    <input type="number"
                                                           name="seconds"
                                                           value="{{ str_pad($movingSeconds, 2, '0', STR_PAD_LEFT) }}"
                                                           class="time-input w-14 px-2 py-1 border border-gray-300 rounded-md text-sm"
                                                           min="0"
                                                           max="59">
                                                    <span>"</span>
                                                </div>
                                                <input type="hidden" name="elapsed_time" value="{{ $activity->elapsed_time }}">
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <span class="text-gray-400 text-xs">
                                (max: {{ $elapsedTime }})
                            </span>
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
    function validateTime(form) {
        const hours = parseInt(form.querySelector('input[name="hours"]').value) || 0;
        const minutes = parseInt(form.querySelector('input[name="minutes"]').value) || 0;
        const seconds = parseInt(form.querySelector('input[name="seconds"]').value) || 0;
        const elapsedTime = parseInt(form.querySelector('input[name="elapsed_time"]').value);

        // Convert to total seconds
        const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

        if (totalSeconds > elapsedTime) {
            alert('Le temps en mouvement doit être inférieur ou égal au temps écoulé');
            return false;
        }

        return true;
    }

    // Ensure two digits for minutes and seconds
    document.querySelectorAll('input[name="minutes"], input[name="seconds"]').forEach(input => {
        input.addEventListener('input', function(e) {
            if (this.value < 10 && this.value.length === 1) {
                this.value = '0' + this.value;
            }
            if (this.value > 59) {
                this.value = '59';
            }
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
    .time-input {
        font-family: monospace;
    }

    .time-input::-webkit-inner-spin-button,
    .time-input::-webkit-outer-spin-button {
        opacity: 1;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    .pagination > * {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
    }

    .pagination a {
        text-decoration: none;
        color: rgb(69, 148, 209);
    }

    .pagination span.current-page {
        background-color: rgb(69, 148, 209);
        color: white;
    }

    .pagination a:hover {
        background-color: rgb(229, 241, 248);
    }
</style>
