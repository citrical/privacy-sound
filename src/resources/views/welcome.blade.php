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
                    <img src="{{ asset('images/privacy-sound-logo.png') }}" alt="Privacy Sound" class="h-16 w-auto">
                </div>

                {{-- Texto central --}}
                <div class="text-center mb-10">
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-4">Privacidad en audio</p>
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
                <div class="w-full opacity-20 pointer-events-none">
                    <svg viewBox="0 0 1200 80" xmlns="http://www.w3.org/2000/svg" class="w-full">
                        <polyline
                            points="0,40 40,40 50,15 60,65 70,5 80,75 90,20 100,60 110,25 120,55 130,35 140,50 150,10 160,70 170,40 200,40 210,20 220,60 230,15 240,65 250,30 260,55 270,40 300,40 310,20 320,60 330,25 340,55 350,40 380,40 390,15 400,65 410,35 420,50 430,40 460,40 470,20 480,60 490,10 500,70 510,40 540,40 550,30 560,55 570,20 580,60 590,40 620,40 630,15 640,65 650,25 660,55 670,40 700,40 710,35 720,50 730,20 740,60 750,40 780,40 790,20 800,60 810,30 820,55 830,40 860,40 870,15 880,65 890,25 900,55 910,40 940,40 950,20 960,60 970,35 980,50 990,40 1020,40 1030,20 1040,60 1050,15 1060,65 1070,40 1100,40 1110,30 1120,55 1130,25 1140,55 1150,40 1200,40"
                            fill="none"
                            stroke="#F24B4B"
                            stroke-width="2"
                        />
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