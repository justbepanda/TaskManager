<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.Create task') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="sm:max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('tasks.Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <!-- Description -->
                        <div class="mb-3">
                            <x-input-label for="description" :value="__('tasks.Description')" />
                            <x-textarea id="description" class="block mt-1 w-full" name="description"  autocomplete="description">
                                {{ old('description') }}
                            </x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <!-- Status_id -->
                        <div class="mb-3">
                            <x-input-label for="status_id" :value="__('tasks.Status')" />
                            <x-select id="status_id" class="block mt-1 w-full" name="status_id">
                                <option value="" selected="selected"></option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                        </div>
                        <!-- Assigned_to_id -->
                        <div class="mb-3">
                            <x-input-label for="assigned_to_id" :value="__('tasks.Assigned to')" />
                            <x-select id="assigned_to_id" class="block mt-1 w-full" name="assigned_to_id">
                                <option value="" selected="selected"></option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error :messages="$errors->get('assigned_to_id')" class="mt-2" />
                        </div>
                        <div class="mb-3">
                            <x-input-label for="labels[]" :value="__('tasks.Labels')" />
                            <x-select-multiple id="labels[]" class="block mt-1 w-full" name="labels[]">
                                <option value="" selected="selected"></option>
                                @foreach ($labels as $label)
                                    <option value="{{ $label->id }}" {{ old('labels[]') == $label->id ? 'selected' : '' }}>{{ $label->name }}</option>
                                @endforeach
                            </x-select-multiple>
                            <x-input-error :messages="$errors->get('labels[]')" class="mt-2" />
                        </div>
                        <input type="hidden" name="created_by_id" value="{{ auth()->user()->id }}">
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('tasks.Create') }}
                            </x-primary-button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
