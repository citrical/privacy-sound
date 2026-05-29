<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Administración
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

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Usuarios registrados</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Audios almacenados</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalAudios }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Uso de almacenamiento</p>
                    <p class="text-3xl font-bold text-gray-900">
                        {{ number_format($totalSize / 1024 / 1024, 2) }} MB
                    </p>
                    <p class="text-xs text-gray-400">{{ $fileCount }} ficheros</p>
                </div>
            </div>

            {{-- Operación transaccional --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Purgar audios procesados</h3>
                    <p class="text-sm text-gray-500 mb-4">Elimina todos los ficheros de audio procesados del servidor, liberando espacio. Los audios originales y los registros se conservan.</p>
                    <form action="{{ route('admin.purge-processed') }}" method="POST">
                        @csrf
                        <x-danger-button onclick="return confirm('¿Purgar todos los audios procesados? Esta acción no se puede deshacer.')">
                            Purgar audios procesados
                        </x-danger-button>
                    </form>
                </div>
            </div>

            {{-- Ficheros huérfanos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Ficheros huérfanos</h3>
                        <a href="{{ route('admin.users') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                            Gestionar usuarios
                        </a>
                    </div>
                    @forelse ($orphanedFiles as $filename)
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <span class="text-sm text-gray-700 font-mono">{{ $filename }}</span>
                            <form action="{{ route('admin.orphans.destroy', $filename) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-danger-button
                                    onclick="return confirm('¿Eliminar este fichero?')"
                                >
                                    Eliminar
                                </x-danger-button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay ficheros huérfanos.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>