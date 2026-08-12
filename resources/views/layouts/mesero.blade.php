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
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            tribu: {
                                gold: '#d4af37',
                                darkBg: '#121619',
                                orange: '#F53003',
                                dark: '#1b1b18',
                                cream: '#FAF4EB',
                                fontColor: '#2c1d11'
                            }
                        },
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans antialiased bg-[#fdfbf7] text-[#2c1d11]" x-data="{ mobileSidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.sidebar-mesero')

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
                <header class="bg-[#121619] border-b border-[#d4af37]/20 h-16 flex items-center justify-between px-6 z-30 shadow-md">
                    <div class="flex items-center gap-4">
                        <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-white hover:text-tribu-gold md:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @isset($header)
                            <div class="text-lg font-serif font-semibold text-[#d4af37] italic">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex flex-col text-right leading-tight hidden sm:flex text-gray-300">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-[#d4af37]">Mesero</span>
                            <span class="text-xs font-semibold">{{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold bg-white/10 hover:bg-white/20 text-[#d4af37] hover:text-white px-3.5 py-2 rounded-xl transition uppercase tracking-wider border border-[#d4af37]/20">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#fdfbf7] p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
