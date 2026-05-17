<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Audios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de éxito --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulario de subida --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Subir nuevo audio</h3>
                    <form action="{{ route('audios.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input
                                type="file"
                                name="audio"
                                accept=".mp3,.wav,.ogg,.m4a,.flac"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                            />
                            <x-primary-button>Subir</x-primary-button>
                        </div>
                        @error('audio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

            {{-- Listado de audios --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis archivos</h3>
                    @forelse ($audios as $audio)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ $audio->original_filename }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ number_format($audio->size / 1024 / 1024, 2) }} MB
                                    &middot;
                                    {{ $audio->created_at->diffForHumans() }}
                                    &middot;
                                    <span class="capitalize">{{ $audio->status }}</span>
                                </p>
                            </div>
                            <form action="{{ route('audios.destroy', $audio) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-danger-button
                                    onclick="return confirm('¿Eliminar este audio?')"
                                >
                                    Eliminar
                                </x-danger-button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500">No tienes audios subidos todavía.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>