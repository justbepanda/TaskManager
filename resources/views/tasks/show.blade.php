<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.task') }}: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-3"><b>{{ __('tasks.name') }}:</b> {{ $task->name }}</div>
                    <div class="mb-3"><b>{{ __('tasks.description') }}:</b> {{ $task->description }}</div>
                    <div class="mb-3"><b>{{ __('tasks.assigned to') }}:</b> {{ $task->performer->name }}</div>
                    <div class="mb-3"><b>{{ __('tasks.created at') }}:</b> {{ $task->created_at->format('d.m.Y') }}</div>
                    <a href="{{ route('tasks.edit', $task) }}">{{ __('tasks.edit') }}</a>
                    <form action="{{ route('tasks.destroy', $task) }}" data-confirm="{{ __('tasks.Are you sure you want to delete?') }}" method="POST"
                          style="display:inline;" >
                        @csrf
                        @method('DELETE')
                        <button type="submit">{{ __('tasks.delete') }}</button>
                    </form>


                </div>
            </div>
        </div>
    </div>


</x-app-layout>
