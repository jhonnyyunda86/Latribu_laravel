<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'La Tribu - Mesero') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans antialiased bg-tribu-cream text-tribu-fontColor" x-data="{ mobileSidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-tribu-darkBg text-gray-300 flex flex-col justify-between transition-transform duration-300 transform md:translate-x-0 md:static md:h-screen"
                :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                
                <div class="flex flex-col flex-1 min-h-0">
                    <!-- Brand / Logo Header -->
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-tribu-gold to-yellow-600 flex items-center justify-center text-tribu-darkBg shadow-md shadow-tribu-gold/10">
                            <!-- Icono de cubiertos vintage -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.258a7.98 7.98 0 00-1 .042v-2.3A1 1 0 0110 2zm1 3.298a8.046 8.046 0 013.916 2.378 1 1 0 01-1.414 1.414 6.045 6.045 0 00-3.327-1.92L11 5.298zm-2 .042v2.3a6.046 6.046 0 00-3.327 1.92 1 1 0 01-1.414-1.414A8.046 8.046 0 019 5.34zm2 5.658c1.332 0 2.54.436 3.514 1.173a1 1 0 11-1.228 1.58A3.997 3.997 0 0011 13v-2zm-2 0V13a3.997 3.997 0 00-2.286.753 1 1 0 11-1.228-1.58A5.997 5.997 0 019 10.998z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-md font-serif font-bold tracking-widest text-white leading-none">LA TRIBU</h2>
                            <span class="text-[9px] tracking-wider text-gray-500 uppercase font-semibold">Sistema Restaurante</span>
                        </div>
                    </div>

                    <!-- User Profile Info -->
                    <div class="px-6 py-6 border-b border-white/5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 rounded-full bg-tribu-gold/10 border-2 border-tribu-gold flex items-center justify-center text-tribu-gold text-lg font-bold shadow-lg shadow-tribu-gold/5 mb-3">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <h3 class="text-sm font-semibold text-white leading-none mb-1">{{ Auth::user()->name }}</h3>
                        <span class="text-[10px] text-tribu-gold uppercase font-bold tracking-wider">Mesero / Staff</span>
                    </div>

                    <!-- Sidebar Navigation Links -->
                    <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-tribu-gold to-yellow-600 text-tribu-darkBg shadow-md shadow-tribu-gold/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="#" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 hover:bg-white/5 hover:text-white transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Menú</span>
                        </a>

                        <a href="#" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 hover:bg-white/5 hover:text-white transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Mesas</span>
                        </a>

                        <a href="#" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 hover:bg-white/5 hover:text-white transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Pedidos</span>
                        </a>

                        <a href="#" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold uppercase tracking-wider text-gray-400 hover:bg-white/5 hover:text-white transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Reservas</span>
                        </a>
                    </nav>
                </div>

                <!-- Log Out Form at the bottom -->
                <div class="p-4 border-t border-white/5 bg-[#0a0c0e]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3 bg-white/5 hover:bg-red-950/20 hover:text-red-400 text-gray-400 rounded-xl text-xs font-bold uppercase tracking-widest transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Backdrop for Mobile -->
            <div class="fixed inset-0 z-40 bg-black/50 md:hidden"
                 x-show="mobileSidebarOpen"
                 @click="mobileSidebarOpen = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Navbar -->
                <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 z-30">
                    <div class="flex items-center gap-4">
                        <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-gray-500 hover:text-gray-700 md:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @isset($header)
                            <div class="text-lg font-serif font-semibold text-tribu-dark italic">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest bg-gray-100 px-3 py-1.5 rounded-full">
                            Mesero Mode
                        </span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-tribu-cream/40 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
