<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('task_statuses.Task statuses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-primary-link-button class="mb-3" :href="route('task_statuses.create')">
                        {{ __('task_statuses.Create new task status') }}
                    </x-primary-link-button>

                    @if($task_statuses->isNotEmpty())
                    <table class="mt-4 w-full">
                        <thead class="border-b-2 border-solid border-black text-left ">
                        <tr>
                            <th class="p-2">{{ __('task_statuses.ID') }}</th>
                            <th class="p-2">{{ __('task_statuses.Name') }}</th>
                            <th class="p-2">{{ __('task_statuses.Created at') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($task_statuses as $task_status)
                            <tr class="border-b border-dashed text-left">
                                <td class="p-2">{{ $task_status->id }}</td>
                                <td class="p-2"><a href="{{ route('task_statuses.show', $task_status) }}">{{ $task_status->name }}</a></td>
                                <td class="p-2">{{ $task_status->created_at->format('d.m.Y') }}</td>
                                <td class="p-2">

                                    <a href="{{ route('task_statuses.edit', $task_status) }}">{{ __('task_statuses.Edit') }}</a>
                                    <form action="{{ route('task_statuses.destroy', $task_status) }}" data-confirm="{{ __('task_statuses.Are you sure you want to delete?') }}" method="POST"
                                          style="display:inline;" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">{{ __('task_statuses.Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    @else
                        <div>{{ __('Task_statuses.there are no task statuses') }}<div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
