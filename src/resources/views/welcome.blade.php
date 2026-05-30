<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Privacy Sound') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col">

        {{-- Hero --}}
        <main class="flex-1 flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-4xl">

                {{-- Logo --}}
                <div class="flex justify-center mb-10">
                    <img src="{{ asset('images/privacy_sound_logo.svg') }}" alt="Privacy Sound" class="h-20 w-auto">
                </div>

                {{-- Texto central --}}
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-light text-gray-900 leading-tight mb-4" style="font-family: 'Roboto', sans-serif;">
                        Censura lo que no debe escucharse
                    </h1>
                    <p class="text-gray-500 text-base leading-relaxed max-w-xl mx-auto">
                        Sube tu grabación, marca los fragmentos sensibles y descarga el archivo con los tonos de censura insertados.
                    </p>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-center gap-4 mb-12">
                    @auth
                        <a href="{{ route('audios.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Ir a mis audios
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Empezar gratis
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Ya tengo cuenta
                        </a>
                    @endauth
                </div>

                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                        <h3 class="font-semibold text-gray-900 text-sm mb-1">Sube tus audios</h3>
                        <p class="text-xs text-gray-500">Soporta MP3, WAV, OGG, M4A y FLAC.</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                        <h3 class="font-semibold text-gray-900 text-sm mb-1">Marca los fragmentos</h3>
                        <p class="text-xs text-gray-500">Editor visual para seleccionar los segmentos a censurar.</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                        <h3 class="font-semibold text-gray-900 text-sm mb-1">Descarga el resultado</h3>
                        <p class="text-xs text-gray-500">Archivo procesado con tonos de censura, listo para compartir.</p>
                    </div>
                </div>

                {{-- Waveform --}}
                {{-- Waveform animada --}}
                <div class="w-full pointer-events-none overflow-hidden">
                    <svg viewBox="0 0 1200 80" xmlns="http://www.w3.org/2000/svg" class="w-full">
                        <style>
                            .wave-bar { transform-origin: center; animation: pulse-bar 1.2s ease-in-out infinite; }
                            .wave-bar:nth-child(2)  { animation-delay: 0.1s; }
                            .wave-bar:nth-child(3)  { animation-delay: 0.2s; }
                            .wave-bar:nth-child(4)  { animation-delay: 0.3s; }
                            .wave-bar:nth-child(5)  { animation-delay: 0.4s; }
                            .wave-bar:nth-child(6)  { animation-delay: 0.5s; }
                            .wave-bar:nth-child(7)  { animation-delay: 0.6s; }
                            .wave-bar:nth-child(8)  { animation-delay: 0.7s; }
                            .wave-bar:nth-child(9)  { animation-delay: 0.8s; }
                            .wave-bar:nth-child(10) { animation-delay: 0.9s; }
                            .wave-bar:nth-child(11) { animation-delay: 1.0s; }
                            .wave-bar:nth-child(12) { animation-delay: 1.1s; }
                            .wave-bar:nth-child(13) { animation-delay: 0.05s; }
                            .wave-bar:nth-child(14) { animation-delay: 0.15s; }
                            .wave-bar:nth-child(15) { animation-delay: 0.25s; }
                            .wave-bar:nth-child(16) { animation-delay: 0.35s; }
                            .wave-bar:nth-child(17) { animation-delay: 0.45s; }
                            .wave-bar:nth-child(18) { animation-delay: 0.55s; }
                            .wave-bar:nth-child(19) { animation-delay: 0.65s; }
                            .wave-bar:nth-child(20) { animation-delay: 0.75s; }
                            @keyframes pulse-bar {
                                0%, 100% { transform: scaleY(0.3); opacity: 0.2; }
                                50%       { transform: scaleY(1);   opacity: 0.6; }
                            }
                        </style>
                        <rect class="wave-bar" x="20"   y="10" width="6" height="60" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="50"   y="20" width="6" height="40" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="80"   y="5"  width="6" height="70" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="110"  y="15" width="6" height="50" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="140"  y="25" width="6" height="30" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="170"  y="8"  width="6" height="64" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="200"  y="18" width="6" height="44" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="230"  y="3"  width="6" height="74" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="260"  y="20" width="6" height="40" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="290"  y="12" width="6" height="56" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="320"  y="28" width="6" height="24" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="350"  y="6"  width="6" height="68" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="380"  y="16" width="6" height="48" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="410"  y="22" width="6" height="36" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="440"  y="4"  width="6" height="72" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="470"  y="14" width="6" height="52" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="500"  y="24" width="6" height="32" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="530"  y="8"  width="6" height="64" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="560"  y="18" width="6" height="44" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="590"  y="2"  width="6" height="76" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="620"  y="20" width="6" height="40" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="650"  y="10" width="6" height="60" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="680"  y="26" width="6" height="28" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="710"  y="6"  width="6" height="68" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="740"  y="16" width="6" height="48" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="770"  y="22" width="6" height="36" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="800"  y="8"  width="6" height="64" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="830"  y="18" width="6" height="44" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="860"  y="4"  width="6" height="72" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="890"  y="14" width="6" height="52" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="920"  y="24" width="6" height="32" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="950"  y="8"  width="6" height="64" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="980"  y="20" width="6" height="40" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="1010" y="10" width="6" height="60" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="1040" y="26" width="6" height="28" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="1070" y="6"  width="6" height="68" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="1100" y="16" width="6" height="48" rx="3" fill="#F25A38"/>
                        <rect class="wave-bar" x="1130" y="12" width="6" height="56" rx="3" fill="#F24B4B"/>
                        <rect class="wave-bar" x="1160" y="22" width="6" height="36" rx="3" fill="#F24B4B"/>
                    </svg>
                </div>

            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Privacy Sound. Todos los derechos reservados.
            </div>
        </footer>

    </body>
</html>