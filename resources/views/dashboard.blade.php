<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a class="mb-5 bg-purple-600 hover:bg-purple-400 p-3 rounded-lg text-white" href="{{ route('chirps.create') }}">Create Chirp</a>
            @foreach($chirps as $chirp)
            <div class="mt-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div>
                            {{$chirp->title}}
                        </div>
                        <div>
                            {{$chirp->description}}
                        </div>
                        <div class="mt-6">
                            <a class="mb-5 bg-purple-600 hover:bg-purple-400 p-3 rounded-lg text-white" href="{{ route('chirps.edit', $chirp->id) }}">Edit Chirp</a>
                            <form method="POST" action="{{ route('chirps.destroy', $chirp->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="mb-5 bg-red-600 hover:bg-red-400 p-3 rounded-lg text-white" type="submit">Delete Chirp</button>
                            </form>
                            <a class="mb-5 bg-cyan-600 hover:bg-purple-400 p-3 rounded-lg text-white" href="{{ route('chirps.show', $chirp->id) }}">View Chirp</a>
                        </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
