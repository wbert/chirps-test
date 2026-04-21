<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chirp') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold">{{$chirp->title}}</h1>
                    <h1>{{$chirp->description}}</h1>

                    <p class="pb-6 font-bold">Comments</p>
                    @foreach($chirp->comments as $comment)
                        <h1 class="text-xl font-bold">{{$comment->user->name}}</h1>
                        <h1>{{$comment->content}}</h1>
                    @endforeach


                    <h1 class="pt-6">Add Comment</h1>
                    <form method="POST" action="{{ route('chirps.comment', $chirp) }}">
                        <input class="text-black"type="text" name="content" />
                        <button type="submit">comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
