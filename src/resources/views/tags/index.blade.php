<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Etiquetas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Formulario nueva etiqueta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nueva etiqueta</h3>
                    <form action="{{ route('tags.store') }}" method="POST">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <x-text-input
                                    name="name"
                                    type="text"
                                    placeholder="Nombre de la etiqueta"
                                    class="w-full"
                                    value="{{ old('name') }}"
                                />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input type="color" name="color" value="{{ old('color', '#6b7280') }}"
                                    class="h-10 w-16 rounded-md border border-gray-300 cursor-pointer">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <x-primary-button>Crear</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listado de etiquetas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis etiquetas</h3>
                    @forelse ($tags as $tag)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center gap-3">
                                <span class="inline-block w-4 h-4 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                <span class="text-gray-900 font-medium">{{ $tag->name }}</span>
                                <span class="text-xs text-gray-400">{{ $tag->audios_count ?? $tag->audios()->count() }} audios</span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Editar --}}
                                <form action="{{ route('tags.update', $tag) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $tag->name }}"
                                        class="text-sm border-gray-300 rounded-md shadow-sm w-32">
                                    <input type="color" name="color" value="{{ $tag->color }}"
                                        class="h-8 w-12 rounded border border-gray-300 cursor-pointer">
                                    <x-secondary-button type="submit">Guardar</x-secondary-button>
                                </form>
                                {{-- Eliminar --}}
                                <form action="{{ route('tags.destroy', $tag) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button onclick="return confirm('¿Eliminar esta etiqueta?')">
                                        Eliminar
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No tienes etiquetas creadas todavía.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>