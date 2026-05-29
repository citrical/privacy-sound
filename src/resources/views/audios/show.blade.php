<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $audio->original_filename }}
            </h2>
            <a href="{{ route('audios.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
            @endif

            {{-- Editar nombre --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información</h3>
                    <form action="{{ route('audios.update', $audio) }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        @method('PATCH')
                        <x-text-input name="original_filename" type="text" class="flex-1" value="{{ $audio->original_filename }}" />
                        <x-secondary-button type="submit">Guardar nombre</x-secondary-button>
                        <span class="text-xs text-gray-400">
                            {{ number_format($audio->size / 1024 / 1024, 2) }} MB
                            · {{ $audio->created_at->diffForHumans() }}
                            · <span class="capitalize">{{ $audio->status }}</span>
                        </span>
                    </form>

                    {{-- Etiquetas --}}
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">Etiquetas:</p>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @forelse ($audio->tags as $tag)
                                <form action="{{ route('audios.tags.destroy', [$audio, $tag]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs text-white hover:opacity-80 transition" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }} ×
                                    </button>
                                </form>
                            @empty
                                <span class="text-xs text-gray-400">Sin etiquetas</span>
                            @endforelse
                        </div>
                        @if (auth()->user()->tags()->whereNotIn('id', $audio->tags->pluck('id'))->exists())
                            <form action="{{ route('audios.tags.store', $audio) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <select name="tag_id" class="text-sm border-gray-300 rounded-md shadow-sm">
                                    @foreach (auth()->user()->tags()->whereNotIn('id', $audio->tags->pluck('id'))->get() as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                <x-secondary-button type="submit">Añadir etiqueta</x-secondary-button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Reproductor WaveSurfer --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div id="waveform" class="mb-4"></div>
                    <div class="flex items-center gap-4 mb-4">
                        <button id="btn-play" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                            ▶ Reproducir
                        </button>
                        <button id="btn-add-region" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition ease-in-out duration-150">
                            + Añadir región
                        </button>
                        <span id="current-time" class="text-sm text-gray-500">0:00</span>
                    </div>
                    <div id="timeline"></div>
                </div>
            </div>

            {{-- Segmentos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Segmentos a censurar</h3>
                    <div id="regions-list">
                        <p class="text-gray-500 text-sm">No hay segmentos definidos. Añade una región en el waveform.</p>
                    </div>

                    {{-- Segmentos guardados en BD --}}
                    @if ($audio->segments->count() > 0)
                        <div class="mt-4 border-t pt-4">
                            <p class="text-xs text-gray-400 mb-2">Segmentos del último procesamiento:</p>
                            @foreach ($audio->segments as $segment)
                                <div class="flex items-center justify-between py-1 text-sm text-gray-600">
                                    <span>{{ number_format($segment->start, 2) }}s — {{ number_format($segment->end, 2) }}s</span>
                                    <form action="{{ route('segments.destroy', $segment) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button onclick="return confirm('¿Eliminar este segmento?')">
                                            Eliminar
                                        </x-danger-button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Procesar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form id="process-form" action="{{ route('audios.process', $audio) }}" method="POST">
                        @csrf
                        <input type="hidden" name="regions" id="regions-input" value="[]">
                        <x-primary-button id="btn-process">Procesar audio</x-primary-button>
                        <p class="mt-2 text-sm text-gray-500">Se insertarán beeps en los segmentos marcados.</p>
                    </form>
                </div>
            </div>

            {{-- Descarga --}}
            @if ($audio->isProcessed())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Audio procesado</h3>
                        <a href="{{ route('audios.download', $audio) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">
                            ⬇ Descargar audio censurado
                        </a>
                    </div>
                </div>
            @endif

            {{-- Notas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notas</h3>
                    @forelse ($audio->notes as $note)
                        <div class="flex items-start justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="text-sm text-gray-700">{{ $note->content }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $note->created_at->diffForHumans() }}</p>
                            </div>
                            <form action="{{ route('notes.destroy', $note) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-danger-button onclick="return confirm('¿Eliminar esta nota?')">
                                    Eliminar
                                </x-danger-button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay notas para este audio.</p>
                    @endforelse

                    <form action="{{ route('notes.store', $audio) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex items-start gap-4">
                            <textarea name="content" rows="2" placeholder="Añade una nota..."
                                class="flex-1 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <x-primary-button type="submit">Añadir</x-primary-button>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    @vite('resources/js/audio-editor.js')
    <script type="module">
        const regions = RegionsPlugin.create()
        const timeline = TimelinePlugin.create({ container: '#timeline' })

        const ws = WaveSurfer.create({
            container: '#waveform',
            waveColor: '#6b7280',
            progressColor: '#F24B4B',
            url: '{{ $audio->isProcessed() ? route('audios.stream-processed', $audio) : route('audios.stream', $audio) }}',
            plugins: [regions, timeline],
        })

        document.getElementById('btn-play').addEventListener('click', () => ws.playPause())
        ws.on('play', () => document.getElementById('btn-play').textContent = '⏸ Pausar')
        ws.on('pause', () => document.getElementById('btn-play').textContent = '▶ Reproducir')
        ws.on('timeupdate', (time) => {
            const m = Math.floor(time / 60)
            const s = Math.floor(time % 60).toString().padStart(2, '0')
            document.getElementById('current-time').textContent = `${m}:${s}`
        })

        document.getElementById('btn-add-region').addEventListener('click', () => {
            const currentTime = ws.getCurrentTime()
            const duration = ws.getDuration()
            regions.addRegion({
                start: currentTime,
                end: Math.min(currentTime + 2, duration),
                color: 'rgba(242, 75, 75, 0.3)',
                drag: true,
                resize: true,
            })
        })

        function updateRegionsList() {
            const list = regions.getRegions()
            const container = document.getElementById('regions-list')
            if (list.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">No hay segmentos definidos. Añade una región en el waveform.</p>'
                document.getElementById('regions-input').value = '[]'
                return
            }
            const sorted = [...list].sort((a, b) => a.start - b.start)
            container.innerHTML = sorted.map((r, i) => `
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <span class="text-sm text-gray-700">Segmento ${i + 1}: ${r.start.toFixed(2)}s — ${r.end.toFixed(2)}s</span>
                    <button onclick="removeRegion('${r.id}')" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                </div>
            `).join('')
            document.getElementById('regions-input').value = JSON.stringify(
                sorted.map(r => ({ start: r.start, end: r.end }))
            )
        }

        regions.on('region-created', updateRegionsList)
        regions.on('region-updated', updateRegionsList)
        regions.on('region-removed', updateRegionsList)
        window.removeRegion = (id) => regions.getRegions().find(r => r.id === id)?.remove()
    </script>
    @endpush
</x-app-layout>