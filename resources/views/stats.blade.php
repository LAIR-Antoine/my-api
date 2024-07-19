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
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Natation</th>
                            <th>Vélo</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($yearStats as $year => $stat)
                            <tr>
                                <td><a href="{{ route('stats.months', $stat->year) }}">{{ $stat->year }}</a></td>
                                <td>
                                    {{ str_replace('.', ',', $stat->swim) }} km
                                    <small>({{ str_replace('.', ',', $yearStatsAtDay[$year]->swim) }} km)</small>
                                </td>
                                <td>
                                    {{ str_replace('.', ',', $stat->bike) }} km
                                    <small>({{ str_replace('.', ',', $yearStatsAtDay[$year]->bike) }} km)</small>
                                </td>
                                <td>
                                    {{ str_replace('.', ',', $stat->run) }} km
                                    <small>({{ str_replace('.', ',', $yearStatsAtDay[$year]->run) }} km)</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

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
        margin: 10px;
        font-weight: bold;  
    }
    .strava-button {
        background-color: rgb(252,82,0);
        
    }
    .blue-button {
        background-color: rgb(69, 148, 209);  
    }
</style>
