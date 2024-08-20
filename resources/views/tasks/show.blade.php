<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.task') }} {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                        <table class="mt-4 w-full">
                            <thead class="border-b-2 border-solid border-black text-left ">
                            <tr>
                                <th class="p-2">{{ __('tasks.id') }}</th>
                                <th class="p-2">{{ __('tasks.name') }}</th>
                                <th class="p-2">{{ __('tasks.created at') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr class="border-b border-dashed text-left">
                                <td class="p-2">{{ $task->id }}</td>
                                <td class="p-2"><a href="{{ route('tasks.show', $task) }}">{{ $task->name }}</a></td>
                                <td class="p-2">{{ $task->created_at }}</td>
                                <td class="p-2">

                                    <a href="{{ route('tasks.edit', $task) }}">{{ __('tasks.edit') }}</a>
                                    <form action="{{ route('tasks.destroy', $task) }}" data-confirm="{{ __('tasks.Are you sure you want to delete?') }}" method="POST"
                                          style="display:inline;" >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">{{ __('tasks.delete') }}</button>
                                    </form>
                                </td>
                            </tr>


                            </tbody>
                        </table>


                </div>
            </div>
        </div>
    </div>


</x-app-layout>
