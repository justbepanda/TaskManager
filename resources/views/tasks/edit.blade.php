<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.edit task') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="sm:max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Name -->
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('tasks.name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $task->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <!-- Description -->
                        <div class="mb-3">
                            <x-input-label for="description" :value="__('tasks.description')" />
                            <x-textarea id="description" class="block mt-1 w-full" name="description" autocomplete="description">
                                {{ old('description', $task->description) }}
                            </x-textarea>

                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <!-- Status_id -->
                        <div class="mb-3">
                            <x-input-label for="status_id" :value="__('tasks.status')" />
                            <x-select id="status_id" class="block mt-1 w-full" name="status_id">
                                <option value="" selected="selected"></option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}"
                                        {{ old('status_id', $task->status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                        </div>
                        <!-- Assigned_to_id -->
                        <div class="mb-3">
                            <x-input-label for="assigned_to_id" :value="__('tasks.assigned to')" />
                            <x-select id="assigned_to_id" class="block mt-1 w-full" name="assigned_to_id">
                                <option value="" selected="selected"></option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to_id', $user->id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('assigned_to_id')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('tasks.update') }}
                            </x-primary-button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
