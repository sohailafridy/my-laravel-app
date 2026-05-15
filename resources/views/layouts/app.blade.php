<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }

            @keyframes fade-in-up {
                from { opacity: 0; transform: translateY(18px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes bar-grow {
                from { width: 0; }
            }

            @keyframes soft-glow {
                0%, 100% { box-shadow: 0 0 0 rgba(20, 184, 166, 0); }
                50% { box-shadow: 0 0 24px rgba(20, 184, 166, 0.18); }
            }

            .motion-card {
                animation: fade-in-up 0.7s ease both;
            }

            .motion-glow {
                animation: soft-glow 3.5s ease-in-out infinite;
            }

            .motion-bar {
                animation: bar-grow 1s ease-out both;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-zinc-100 text-zinc-950">
        <div
            x-data="{ sidebarOpen: false, inventoryOpen: false }"
            class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]"
        >
            @include('layouts.sidebar')

            <main class="min-w-0">
                @include('layouts.dashboard_headbar')

                @if (isset($header))
                    <header class="border-b border-zinc-200 bg-white">
                        <div class="px-4 py-5 sm:px-6 lg:px-8">
                            <div class="mx-auto max-w-7xl">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endif

                <div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
