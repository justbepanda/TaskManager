<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('labels.Edit label') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="sm:max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('labels.update', $label->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('labels.Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $label->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('labels.Update') }}
                            </x-primary-button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
