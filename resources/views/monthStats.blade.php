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
                    <div class="mb-6">
                        <a href="{{ route('stats') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour aux statistiques par année
                        </a>
                    </div>

                    @php
                        // Calculate max values for each sport in the current year
                        $maxSwim = 0;
                        $maxBike = 0;
                        $maxRun = 0;

                        foreach ($monthStats as $stat) {
                            $maxSwim = max($maxSwim, $stat->swim);
                            $maxBike = max($maxBike, $stat->bike);
                            $maxRun = max($maxRun, $stat->run);
                        }
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $year }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-blue-600">Natation</span>
                                    @if($maxSwim > 0)
                                        <span class="text-gray-400 text-xs font-normal lowercase">
                                                (max: {{ str_replace('.', ',', $maxSwim) }} km)
                                            </span>
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-green-600">Vélo</span>
                                    @if($maxBike > 0)
                                        <span class="text-gray-400 text-xs font-normal lowercase">
                                                (max: {{ str_replace('.', ',', $maxBike) }} km)
                                            </span>
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="text-orange-600">Course</span>
                                    @if($maxRun > 0)
                                        <span class="text-gray-400 text-xs font-normal lowercase">
                                                (max: {{ str_replace('.', ',', $maxRun) }} km)
                                            </span>
                                    @endif
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($monthStats as $stat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        @switch($stat->month)
                                            @case(1) Janvier @break
                                            @case(2) Février @break
                                            @case(3) Mars @break
                                            @case(4) Avril @break
                                            @case(5) Mai @break
                                            @case(6) Juin @break
                                            @case(7) Juillet @break
                                            @case(8) Août @break
                                            @case(9) Septembre @break
                                            @case(10) Octobre @break
                                            @case(11) Novembre @break
                                            @case(12) Décembre @break
                                            @default {{ $stat->month }}
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm mb-2">
                                            @if($stat->swim > 0)
                                                <span class="text-blue-600 font-medium">
                                                        {{ str_replace('.', ',', $stat->swim) }} km
                                                    </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                        @if($maxSwim > 0 && $stat->swim > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($stat->swim / $maxSwim) * 100) }}%">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm mb-2">
                                            @if($stat->bike > 0)
                                                <span class="text-green-600 font-medium">
                                                        {{ str_replace('.', ',', $stat->bike) }} km
                                                    </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                        @if($maxBike > 0 && $stat->bike > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full"
                                                     style="width: {{ min(100, ($stat->bike / $maxBike) * 100) }}%">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm mb-2">
                                            @if($stat->run > 0)
                                                <span class="text-orange-600 font-medium">
                                                        {{ str_replace('.', ',', $stat->run) }} km
                                                    </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                        @if($maxRun > 0 && $stat->run > 0)
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-orange-600 h-1.5 rounded-full"
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
