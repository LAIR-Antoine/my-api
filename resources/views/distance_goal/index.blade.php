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
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-800">Mes objectifs</h1>
                        <a href="{{ route('distance_goal.create') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            + Ajouter un objectif
                        </a>
                    </div>

                    @php
                        $sportOrder = ['swim' => 1, 'bike' => 2, 'run' => 3];

                        $activeGoals = $distanceGoals->where('state', 'active')
                            ->sort(function($a, $b) use ($sportOrder) {
                                $dateCompare = strtotime($a->begin_date) - strtotime($b->begin_date);
                                return $dateCompare === 0
                                    ? $sportOrder[$a->sport] - $sportOrder[$b->sport]
                                    : $dateCompare;
                            });

                        $futureGoals = $distanceGoals->where('state', 'future')
                            ->sort(function($a, $b) use ($sportOrder) {
                                $dateCompare = strtotime($a->begin_date) - strtotime($b->begin_date);
                                return $dateCompare === 0
                                    ? $sportOrder[$a->sport] - $sportOrder[$b->sport]
                                    : $dateCompare;
                            });

                        $pastGoals = $distanceGoals->where('state', 'past')
                            ->sort(function($a, $b) use ($sportOrder) {
                                $dateCompare = strtotime($a->begin_date) - strtotime($b->begin_date);
                                return $dateCompare === 0
                                    ? $sportOrder[$a->sport] - $sportOrder[$b->sport]
                                    : $dateCompare;
                            });
                    @endphp

                    @foreach ([
                        'En cours' => $activeGoals,
                        'À venir' => $futureGoals,
                        'Terminés' => $pastGoals
                    ] as $title => $goals)
                        @if($goals->count() > 0)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ $title }}</h2>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="2">Objectifs</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="2">Dates</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($goals as $distanceGoal)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @switch($distanceGoal->sport)
                                                        @case('swim')
                                                            <span class="text-blue-600">Natation</span>
                                                            @break
                                                        @case('bike')
                                                            <span class="text-green-600">Vélo</span>
                                                            @break
                                                        @case('run')
                                                            <span class="text-orange-600">Course</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    {{ str_replace('.', ',', $distanceGoal->distance_done) }} km
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    / {{ $distanceGoal->distance_to_do }} km
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ date("d/m/Y", strtotime($distanceGoal->begin_date)) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ date("d/m/Y", strtotime($distanceGoal->end_date)) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @switch($distanceGoal->state)
                                                        @case('active')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                    En cours
                                                                </span>
                                                            @break
                                                        @case('future')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                    À venir
                                                                </span>
                                                            @break
                                                        @case('past')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                    Terminé
                                                                </span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <div class="flex space-x-3">
                                                        <a href="{{ route('distance_goal.edit', $distanceGoal->id) }}"
                                                           class="inline-flex items-center px-3 py-1 border border-indigo-600 text-indigo-600 rounded-md hover:bg-indigo-600 hover:text-white transition-colors duration-200">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Modifier
                                                        </a>
                                                        <form action="{{ route('distance_goal.destroy', $distanceGoal->id) }}"
                                                              method="POST"
                                                              class="inline"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-3 py-1 border border-red-600 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-colors duration-200">
                                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
