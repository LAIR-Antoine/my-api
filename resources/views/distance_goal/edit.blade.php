@section('title')
    Objectifs
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
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('distance_goal.update', $distanceGoal->id) }}" method="POST" class="space-y-4 max-w-md">
                        @csrf
                        @method('PUT')
                        <div class="space-y-1">
                            <label for="sport" class="block text-sm font-medium text-gray-700">Sport</label>
                            <select id="sport" name="sport" required class="mt-1 h-8 w-64 pl-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @if ($distanceGoal->sport == 'swim')
                                    <option value="swim" selected>Natation</option>
                                    <option value="bike">Vélo</option>
                                    <option value="run">Course</option>
                                @elseif ($distanceGoal->sport == 'bike')
                                    <option value="swim">Natation</option>
                                    <option value="bike" selected>Vélo</option>
                                    <option value="run">Course</option>
                                @elseif ($distanceGoal->sport == 'run')
                                    <option value="swim">Natation</option>
                                    <option value="bike">Vélo</option>
                                    <option value="run" selected>Course</option>
                                @endif
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label for="distance_to_do" class="block text-sm font-medium text-gray-700">Distance à parcourir</label>
                            <input type="number" id="distance_to_do" name="distance_to_do" value={{ $distanceGoal->distance_to_do }} required class="mt-1 h-8 w-64 pl-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <div class="space-y-1">
                            <label for="begin_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" id="begin_date" name="begin_date" value="{{ \Carbon\Carbon::parse($distanceGoal->begin_date)->format('Y-m-d') }}" required class="mt-1 h-8 w-64 pl-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                   pattern="\d{2}/\d{2}/\d{4}" placeholder="jj/mm/aaaa">
                        </div>

                        <div class="space-y-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($distanceGoal->end_date)->format('Y-m-d') }}" required class="mt-1 h-8 w-64 pl-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                   pattern="\d{2}/\d{2}/\d{4}" placeholder="jj/mm/aaaa">
                        </div>

                        <div class="space-y-1">
                            <label for="state" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select id="state" name="state" required class="mt-1 h-8 w-64 pl-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @if ($distanceGoal->state == 'active')
                                    <option value="active" selected>En cours</option>
                                    <option value="future">Futur</option>
                                    <option value="achieved">Terminé</option>
                                @elseif ($distanceGoal->state == 'future')
                                    <option value="active">En cours</option>
                                    <option value="future" selected>Futur</option>
                                    <option value="achieved">Terminé</option>
                                @elseif ($distanceGoal->state == 'past')
                                    <option value="active">En cours</option>
                                    <option value="future">Futur</option>
                                    <option value="achieved" selected>Terminé</option>
                                @endif
                            </select>
                        </div>

                        <input type="number" id="distance_done" name="distance_done" value={{ $distanceGoal->distance_done }} hidden>

                        <button type="submit" class="!bg-gray-800 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-sm">
                            Modifier l'objectif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format dates on load
        formatDates();

        // Add event listeners for date inputs
        document.getElementById('begin_date').addEventListener('change', formatDates);
        document.getElementById('end_date').addEventListener('change', formatDates);
    });

    function formatDates() {
        const dateInputs = ['begin_date', 'end_date'];

        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input.value) {
                const date = new Date(input.value);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                // Keep the HTML5 date input format for the actual value
                input.value = `${year}-${month}-${day}`;

                // Show the formatted date in French format in a custom attribute
                input.setAttribute('data-formatted', `${day}/${month}/${year}`);
            }
        });
    }
</script>

<style>
    /* Show the formatted date instead of the default format */
    input[type="date"]::-webkit-datetime-edit {
        display: none;
    }

    input[type="date"]::before {
        content: attr(data-formatted);
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: black;
    }

    /* Show the default format when input is focused */
    input[type="date"]:focus::-webkit-datetime-edit {
        display: block;
    }

    input[type="date"]:focus::before {
        display: none;
    }

    /* Fix for datepicker icon alignment */
    input[type="date"] {
        position: relative;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        padding-right: 8px;
        height: 20px;
    }
</style>
