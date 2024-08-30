<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.Task') }}: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-3"><b>{{ __('tasks.Name') }}:</b> {{ $task->name }}</div>
                    <div class="mb-3"><b>{{ __('tasks.Description') }}:</b> {{ $task->description }}</div>
                    <div class="mb-3"><b>{{ __('tasks.Assigned to') }}:</b> {{ $task->performer->name }}</div>
                    <div class="mb-3"><b>{{ __('tasks.Labels') }}:</b>

                        @foreach($task->labels as $label)
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $label->name }}</span>
                        @endforeach

                    </div>

                    @if (Route::has('login'))
                        <a href="{{ route('tasks.edit', $task) }}">{{ __('tasks.Edit') }}</a>
                    @endif
                    @can('delete', $task)
                        <form action="{{ route('tasks.destroy', $task) }}"
                              data-confirm="{{ __('tasks.Are you sure you want to delete?') }}" method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">{{ __('tasks.Delete') }}</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
