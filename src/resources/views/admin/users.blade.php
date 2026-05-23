<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Usuarios
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 pr-4 font-medium text-gray-700">Nombre</th>
                                <th class="py-2 pr-4 font-medium text-gray-700">Email</th>
                                <th class="py-2 pr-4 font-medium text-gray-700">Rol</th>
                                <th class="py-2 pr-4 font-medium text-gray-700">Audios</th>
                                <th class="py-2 font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b last:border-0">
                                    <td class="py-3 pr-4 text-gray-900">{{ $user->name }}</td>
                                    <td class="py-3 pr-4 text-gray-500">{{ $user->email }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="capitalize px-2 py-1 rounded text-xs font-medium
                                            {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-500">{{ $user->audios_count }}</td>
                                    <td class="py-3">
                                        @unless ($user->isAdmin())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button
                                                    onclick="return confirm('¿Eliminar usuario {{ $user->name }} y todos sus audios?')"
                                                >
                                                    Eliminar
                                                </x-danger-button>
                                            </form>
                                        @endunless
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>