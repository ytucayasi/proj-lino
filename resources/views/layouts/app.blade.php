<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- WireUI -->
    @wireUiScripts

    <!-- Livewire CSS -->
    @livewireStyles

    <!-- JS and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="dark:bg-secondary-900 bg-white text-secondary-500 dark:text-secondary-400">
    <div class="flex flex-col h-screen" x-data="themeSwitcher()" x-init="init()">
        <div class="border-b-[1px] dark:border-slate-700 border-slate-200">

            <!-- Navbar -->
            <livewire:layout.navbar />
        </div>

        <div class="flex flex-1 max-w-7xl mx-auto w-full overflow-hidden">

            <!-- Sidebar -->
            <livewire:layout.sidebar /> <!-- dark:bg-secondary-800 -->
            <div
                class="w-full overflow-auto no-scrollbar flex flex-col px-8 py-6 dark:bg-secondary-800 bg-secondary-50">

                <!-- Content -->
                <div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    <!-- Livewire JS -->
    @livewireScripts

    <!-- JS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeSwitcher', () => ({
                darkMode: false,
                init() {
                    this.darkMode = localStorage.getItem('darkMode') === 'true';
                    this.updateTheme();
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    this.updateTheme();
                },
                updateTheme() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }));
        });
    </script>
</body>

</html>