<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Chirp') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('chirps.store') }}">
                    @csrf
                    <div class="mb-4 ">
                        <label for="title" class="text-gray-100">Title</label>
                        <div class="p-2">
                            <input class="text-gray-900 rounded-lg" type="text" name="title"/>
                            @error('title')
                            <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4 ">
                        <label for="description" class="text-gray-100">Description</label>
                        <div class="p-2">
                            <input class="text-gray-900 rounded-lg" type="text" name="description"/>
                            @error('description')
                            <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4 ">
                        <button class="bg-purple-600 hover:bg-purple-400 p-3 rounded-lg" type="submit">Post</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
