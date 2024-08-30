<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @auth
                        <x-primary-link-button class="mb-3" :href="route('tasks.create')">
                            {{ __('tasks.Create new task') }}
                        </x-primary-link-button>
                    @endauth


                    <form action="{{ route('tasks.index') }}" method="GET">
                        <div class="filter w-full flex items-center">
                            <div class="mr-3">
                                <x-select name="filter[status_id]" id="filter[status_id]" class="sm:text-sm">
                                    <option value="">{{ __('tasks.Status') }}</option>
                                    @foreach($taskStatuses as $status)
                                        <option
                                            value="{{ $status->id }}" {{ request('filter.status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>
                            <div class="mr-3">
                                <x-select name="filter[created_by_id]" id="filter[created_by_id]"
                                          class="sm:text-sm">
                                    <option value="">{{ __('tasks.Author') }}</option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}" {{ request('filter.created_by_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>
                            <div class="mr-3">
                                <x-select name="filter[assigned_to_id]" id="filter[assigned_to_id]"
                                          class="sm:text-sm">
                                    <option value="">{{ __('tasks.Assigned to') }}</option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}" {{ request('filter.assigned_to_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>
                            <div>
                                <x-secondary-button class=""
                                                    type="submit">{{ __('tasks.Apply') }}</x-secondary-button>
                            </div>
                        </div>
                    </form>
                    <table class="mt-4 w-full">
                        <thead class="border-b-2 border-solid border-black text-left ">
                        <tr>
                            <th class="p-2">{{ __('tasks.ID') }}</th>
                            <th class="p-2">{{ __('tasks.Status') }}</th>
                            <th class="p-2">{{ __('tasks.Name') }}</th>
                            <th class="p-2">{{ __('tasks.Author') }}</th>
                            <th class="p-2">{{ __('tasks.Assigned to') }}</th>
                            <th class="p-2">{{ __('tasks.Created at') }}</th>
                            <th class="p-2">{{ __('tasks.Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($tasks as $task)
                            <tr class="border-b border-dashed text-left">
                                <td class="p-2">{{ $task->id }}</td>
                                <td class="p-2">{{ $task->status->name }}</td>
                                <td class="p-2">
                                    <x-link href="{{ route('tasks.show', $task) }}">{{ $task->name }}</x-link>
                                </td>
                                <td class="p-2">{{ $task->creator->name }}</td>
                                <td class="p-2">
                                    @if( isset($task->performer))
                                        {{ $task->performer->name }}
                                    @endif
                                </td>
                                <td class="p-2">{{ $task->created_at->format('d.m.Y') }}</td>
                                <td class="p-2">
                                    @can('update', $task)
                                        <a href="{{ route('tasks.edit', $task) }}">{{ __('tasks.Edit') }}</a>
                                    @endcan
                                    @can('delete', $task)
                                        {{--                                            <form action="{{ route('tasks.destroy', $task) }}"--}}
                                        {{--                                                  data-confirm="{{ __('tasks.Are you sure you want to delete?') }}"--}}
                                        {{--                                                  method="POST"--}}
                                        {{--                                                  style="display:inline;">--}}
                                        {{--                                                @csrf--}}
                                        {{--                                                @method('DELETE')--}}
                                        {{--                                                <button type="submit">{{ __('tasks.Delete') }}</button>--}}
                                        {{--                                            </form>--}}
                                        <form id="delete-form-{{ $task->id }}"
                                              action="{{ route('tasks.destroy', $task) }}"
                                              method="POST"
                                              style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <a href="#"
                                           onclick="event.preventDefault(); document.getElementById('delete-form-{{ $task->id }}').submit();"
                                           data-confirm="{{ __('tasks.Are you sure you want to delete?') }}"
                                           class="delete-link">
                                            {{ __('tasks.Delete') }}
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <!-- Пагинация -->
                    <div class="mt-3">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>


</x-app-layout>
