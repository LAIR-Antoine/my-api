<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Habitudes pour une journée') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('habbits.store') }}" method="POST">
                        @csrf
                        <div>
                            <input type="date" name="day" id="day" required onchange="loadHabits()">
                        </div>

                        <div id="habits-list">
                            @foreach ($habits as $habit)
                            <div>
                                <input type="checkbox" name="habbits[]" value="{{ $habit->id }}" id="habit_{{ $habit->id }}" onchange="toggleTimeInput(this, '{{ $habit->id }}')">
                                <label for="habit_{{ $habit->id }}">{{ $habit->name }}</label>
                                @if ($habit->info == 'needTime')
                                    <input type="text" name="times[{{ $habit->id }}]" id="time_{{ $habit->id }}" placeholder="Temps" style="display: none;">
                                @endif
                            </div>
                            @endforeach
                        </div>
                        <br />
                        <button class="submit-button" type="submit">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function loadHabits() {
    var day = document.getElementById('day').value;
    if (day) {
        fetch(`/habbits/day/${day}`)
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('#habits-list input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                document.querySelectorAll('#habits-list input[type="text"]').forEach(input => {
                    input.style.display = 'none';
                    input.value = ''; // Réinitialise la valeur
                });
                data.forEach(habit => {
                    var checkbox = document.getElementById(`habit_${habit.habbit_id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                        var timeInput = document.getElementById(`time_${habit.habbit_id}`);
                        if (habit.time && timeInput) {
                            timeInput.style.display = 'block';
                            timeInput.value = habit.time;
                        }
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }
}

function toggleTimeInput(checkbox, habitId) {
    var timeInput = document.getElementById(`time_${habitId}`);
    if (checkbox.checked) {
        timeInput.style.display = 'block';
    } else {
        timeInput.style.display = 'none';
    }
}

</script>

<style>
    .submit-button {
        background-color: #3490dc !important;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
    }

    [type='checkbox']:checked:hover, [type='checkbox']:checked:focus, [type='radio']:checked:hover, [type='radio']:checked:focus {
        background-color: #3490dc !important;
    }

    input {
        margin: 5px;
    }

    input[type="text"], input[type="date"] {
        border: #3490dc 1px solid;
        border-radius: 8px;
    }
</style>
