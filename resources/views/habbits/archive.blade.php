@section('title')
    Habitudes
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Habitudes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="scrollable-table">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sticky-column"></th>
                                    @foreach ($days as $day)
                                        <th>{{ $day->number }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($habits as $habit)
                                    @php
                                        // Déterminer si le temps est utilisé pour l'habitude courante
                                        $timeUsed = false;
                                        foreach ($days as $day) {
                                            $record = $habit->days->firstWhere('id', $day->id);
                                            if ($record && !is_null($record->pivot->time)) {
                                                $timeUsed = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="sticky-column" {{ $timeUsed ? 'rowspan=2' : '' }}>{{ $habit->name }}
                                        </td>
                                        @foreach ($days as $day)
                                            @php
                                                $record = $habit->days->firstWhere('id', $day->id);
                                                if ($habit->begin_date && $habit->end_date) {
                                                    $isActive =
                                                        $habit->begin_date <= $day->date &&
                                                        $habit->end_date >= $day->date;
                                                } elseif ($habit->begin_date) {
                                                    $isActive = $habit->begin_date <= $day->date;
                                                } elseif ($habit->end_date) {
                                                    $isActive = $habit->end_date >= $day->date;
                                                }
                                            @endphp
                                            <td class="record">
                                                @if ($isActive)
                                                    @if ($record)
                                                        <span class="ok">&#10004;</span>
                                                    @else
                                                        <span class="notok">&#10006;</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @if ($timeUsed)
                                        <tr>
                                            @foreach ($days as $day)
                                                @php
                                                    $record = $habit->days->firstWhere('id', $day->id);
                                                @endphp
                                                <td class="record">
                                                    @if ($record && $isActive)
                                                        {{ $record->pivot->time ?? '' }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="sticky-column"></th>
                                    @foreach ($days as $day)
                                        <th>{{ date("d/m", strtotime($day->date)) }}</th>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .scrollable-table {
        overflow-x: auto;
        display: block;
        display: flex;
        flex-direction: row-reverse;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 5px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
    }

    .record {
        align-items: flex-start;
        width: 50px;
    }

    .sticky-column {
        position: sticky;
        left: 0;
        background-color: #FFF;
        z-index: 1;
        min-width: 180px;
    }

    /* Ajoutez ceci pour rendre le fond des thème légèrement différent afin de distinguer la colonne fixe */
    .sticky-column {
        background-color: #f0f0f0;
    }

    .ok {
        background-color: rgb(190, 255, 190) !important;
        padding: 2px;
        border-radius: 2px;
    }

    .notok {
        background-color: rgb(255, 190, 190) !important;
        padding: 2px;
        border-radius: 2px;
    }

    tfoot th {
        font-size: 12px;
    }

    tfoot th {
        font-size: 12px;
    }

    .scrollable-table::-webkit-scrollbar {
    width: 5px;
    }

    .scrollable-table::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey; 
    border-radius: 10px;
    }
    
    .scrollable-table::-webkit-scrollbar-thumb {
    background: #3490dc; 
    border-radius: 10px;
    }

    .scrollable-table::-webkit-scrollbar-thumb:hover {
    background: #b30000; 
    }

    @media (max-width: 768px) {
        table {
            font-size: 10px;
        }

        .sticky-column {
            min-width: 110px;
        }
    }

    .btn-primary {
        background-color: #3490dc;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
    }
</style>
