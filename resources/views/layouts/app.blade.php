<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Dark Mode FOUC Prevention Script -->
        <script>
            (function() {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4);
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.5);
        }
        </style>

        <title>{{ config('app.name', 'JAGOAN') }}</title>

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between transition-colors duration-300">

        <!-- Top Header Component (Always visible across all pages) -->
        <x-top-header />

        <div class="pb-24 max-w-4xl mx-auto w-full flex-1">
            @if(session('success'))
                <div class="mx-4 mt-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <span class="font-medium">{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:opacity-75">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 mt-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <span class="font-medium">{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:opacity-75">✕</button>
                </div>
            @endif
            @yield('content')
        </div>

        <!-- Global Modal & Theme Scripts -->
        <script>
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }
            function formatCurrencyInput(input) {
                if (!input) return;
                let value = input.value.replace(/[^\d]/g, '');
                if (!value) {
                    input.value = '';
                    return;
                }
                input.value = new Intl.NumberFormat('id-ID').format(value);
            }
            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                if (window.syncHeaderThemeIcons) window.syncHeaderThemeIcons();
                if (window.renderAllCharts) window.renderAllCharts();
            }
        </script>
    </body>    
</html>
