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
                    <h1 class="text-3xl font-bold">Mes objectifs</h1>
                    <a href="{{ route('distance_goal.create') }}" class="text-blue-500 hover:text-blue-800">Ajouter un objectif</a>
                    <table class="table-auto border-collapse border border-slate-500">
                        <thead>
                            <tr>
                                <th class="border border-slate-600">Sport</th>
                                <th class="border border-slate-600" colspan="2">Objectifs</th>
                                <th class="border border-slate-600" colspan="2">Dates</th>
                                <th class="border border-slate-600">Statut</th>
                                <th class="border border-slate-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($distanceGoals as $distanceGoal)
                                <tr>
                                    <td class="border border-slate-600">{{ $distanceGoal->sport }}</td>
                                    <td class="border border-slate-600">{{ str_replace('.', ',', $distanceGoal->distance_done) }} km</td>
                                    <td class="border border-slate-600">{{ $distanceGoal->distance_to_do }} km</td>
                                    <td class="border border-slate-600">{{ date("d/m/Y", strtotime($distanceGoal->begin_date)) }}</td>
                                    <td class="border border-slate-600">{{ date("d/m/Y", strtotime($distanceGoal->end_date)) }}</td>
                                    <td class="border border-slate-600">{{ $distanceGoal->state }}</td>
                                    <td class="border border-slate-600">
                                        <a href="{{ route('distance_goal.edit', $distanceGoal->id) }}" class="text-blue-500 hover:text-blue-800">Modifier</a>
                                        <form action="{{ route('distance_goal.destroy', $distanceGoal->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-800">Supprimer</button>
                                        </form>
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
    td {
        padding: 1rem;
        }
</style>
