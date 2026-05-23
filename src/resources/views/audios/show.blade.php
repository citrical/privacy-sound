<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $audio->original_filename }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Reproductor WaveSurfer --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div id="waveform" class="mb-4"></div>

                    <div class="flex items-center gap-4 mb-4">
                        <button
                            id="btn-play"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150"
                        >
                            ▶ Reproducir
                        </button>
                        <button
                            id="btn-add-region"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition ease-in-out duration-150"
                        >
                            + Añadir región
                        </button>
                        <span id="current-time" class="text-sm text-gray-500">0:00</span>
                    </div>

                    {{-- Timeline --}}
                    <div id="timeline"></div>
                </div>
            </div>

            {{-- Lista de regiones --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Segmentos a censurar</h3>
                    <div id="regions-list">
                        <p class="text-gray-500 text-sm">No hay segmentos definidos. Añade una región en el waveform.</p>
                    </div>
                </div>
            </div>

            {{-- Formulario de procesamiento --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="process-form" action="{{ route('audios.process', $audio) }}" method="POST">
                        @csrf
                        <input type="hidden" name="regions" id="regions-input" value="[]">
                        <x-primary-button id="btn-process">
                            Procesar audio
                        </x-primary-button>
                        <p class="mt-2 text-sm text-gray-500">
                            Se insertarán beeps en los segmentos marcados.
                        </p>
                    </form>
                </div>
            </div>

            {{-- Descarga --}}
            @if ($audio->isProcessed())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Audio procesado</h3>
                        <a href="{{ route('audios.download', $audio) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">
                            ⬇ Descargar audio censurado
                        </a>
                    </div>
                </div>
            @endif

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

    // Play/pause
    document.getElementById('btn-play').addEventListener('click', () => {
        ws.playPause()
    })

    ws.on('play', () => document.getElementById('btn-play').textContent = '⏸ Pausar')
    ws.on('pause', () => document.getElementById('btn-play').textContent = '▶ Reproducir')
    ws.on('timeupdate', (time) => {
        const m = Math.floor(time / 60)
        const s = Math.floor(time % 60).toString().padStart(2, '0')
        document.getElementById('current-time').textContent = `${m}:${s}`
    })

    // Añadir región
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

    // Actualizar lista de regiones y formulario
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
                <span class="text-sm text-gray-700">
                    Segmento ${i + 1}: ${r.start.toFixed(2)}s — ${r.end.toFixed(2)}s
                </span>
                <button
                    onclick="removeRegion('${r.id}')"
                    class="text-sm text-red-600 hover:text-red-800"
                >
                    Eliminar
                </button>
            </div>
        `).join('')

        document.getElementById('regions-input').value = JSON.stringify(
            sorted.map(r => ({ start: r.start, end: r.end }))
        )
    }

    regions.on('region-created', updateRegionsList)
    regions.on('region-updated', updateRegionsList)
    regions.on('region-removed', updateRegionsList)

    window.removeRegion = (id) => {
        regions.getRegions().find(r => r.id === id)?.remove()
    }
</script>
@endpush
</x-app-layout>