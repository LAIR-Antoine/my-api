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
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('distance_goal.update', $distanceGoal->id)  }}" method="POST"> 
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="sport">Sport</label>
                            <select class="form-control" id="sport" name="sport" required>
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

                        <div class="form-group">
                            <label for="distance_to_do">Distance à parcourir</label>
                            <input type="number" class="form-control" id="distance_to_do" name="distance_to_do" value={{ $distanceGoal->distance_to_do }} required>
                        </div>

                        <div class="form-group">
                            <label for="begin_date">Date de début</label>
                            <input type="date" class="form-control" id="begin_date" name="begin_date" value={{ $distanceGoal->begin_date }} required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value={{ $distanceGoal->end_date }} required>
                        </div>

                        <div class="form-group">
                            <label for="state">Statut</label>
                            <select class="form-control" id="state" name="state" required>
                                @if ($distanceGoal->state == 'active')
                                    <option value="active" selected>En cours</option>
                                    <option value="future">Futur</option>
                                    <option value="achieved">Terminé</option>
                                @elseif ($distanceGoal->state == 'future')
                                    <option value="active">En cours</option>
                                    <option value="future" selected>Futur</option>
                                    <option value="achieved">Terminé</option>
                                @elseif ($distanceGoal->state == 'achieved')
                                    <option value="active">En cours</option>
                                    <option value="future">Futur</option>
                                    <option value="achieved" selected>Terminé</option>
                                @endif
                            </select>
                        </div>

                        <input class="form-control" id="distance_done" name="distance_done" value={{ $distanceGoal->distance_done }} hidden>

                        <button type="submit" class="btn btn-primary">Modifier l'objectif</button>
                    </form>
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

<style>
    input[type="text"],
    input[type="date"],
    input[type="checkbox"],
    input[type="time"] {
        background-color: transparent;
    }

    .checkbox-list label {
        margin-right: 20px;
    }

    .form-group {
        padding: 5px
    }
</style>
