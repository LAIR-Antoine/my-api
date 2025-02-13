@section('title')
    Statistiques
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        @php
                            // Calculate max values for each sport
                            $maxSwim = 0;
                            $maxBike = 0;
                            $maxRun = 0;

                            foreach ($yearStats as $stat) {
                                $maxSwim = max($maxSwim, $stat->swim);
                                $maxBike = max($maxBike, $stat->bike);
                                $maxRun = max($maxRun, $stat->run);
                            }
                        @endphp

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Année
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-blue-600">Natation</span>
                                    <span class="text-gray-400 text-xs font-normal lowercase">
                                            (max: {{ str_replace('.', ',', $maxSwim) }} km)
                                        </span>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-green-600">Vélo</span>
                                    <span class="text-gray-400 text-xs font-normal lowercase">
                                            (max: {{ str_replace('.', ',', $maxBike) }} km)
                                        </span>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-orange-600">Course</span>
                                    <span class="text-gray-400 text-xs font-normal lowercase">
                                            (max: {{ str_replace('.', ',', $maxRun) }} km)
                                        </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($yearStats as $year => $stat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('stats.months', $stat->year) }}"
                                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $stat->year }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ str_replace('.', ',', $stat->swim) }} km</span>
                                            <span class="text-gray-500 ml-2">
                                                    ({{ str_replace('.', ',', $yearStatsAtDay[$year]->swim) }} km)
                                                </span>
                                        </div>
                                        @if($maxSwim > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <div class="bg-blue-600 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($yearStatsAtDay[$year]->swim / $maxSwim) * 100) }}%">
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div class="bg-blue-400 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($stat->swim / $maxSwim) * 100) }}%">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ str_replace('.', ',', $stat->bike) }} km</span>
                                            <span class="text-gray-500 ml-2">
                                                    ({{ str_replace('.', ',', $yearStatsAtDay[$year]->bike) }} km)
                                                </span>
                                        </div>
                                        @if($maxBike > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <div class="bg-green-600 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($yearStatsAtDay[$year]->bike / $maxBike) * 100) }}%">
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div class="bg-green-400 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($stat->bike / $maxBike) * 100) }}%">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ str_replace('.', ',', $stat->run) }} km</span>
                                            <span class="text-gray-500 ml-2">
                                                    ({{ str_replace('.', ',', $yearStatsAtDay[$year]->run) }} km)
                                                </span>
                                        </div>
                                        @if($maxRun > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <div class="bg-orange-600 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($yearStatsAtDay[$year]->run / $maxRun) * 100) }}%">
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div class="bg-orange-400 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($stat->run / $maxRun) * 100) }}%">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
