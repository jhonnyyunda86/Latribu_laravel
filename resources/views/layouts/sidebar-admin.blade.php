<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#121619] text-gray-300 flex flex-col justify-between transition-transform duration-300 transform md:translate-x-0 md:sticky md:top-0 md:h-screen border-r border-[#d4af37]/20"
    :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <div class="flex flex-col flex-1 min-h-0">
        <!-- Brand / Logo Header -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/5">
            <div class="w-12 h-12 rounded-2xl bg-[#d4af37] flex items-center justify-center text-[#121619] shadow-md">
                <!-- Icono de cubiertos -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </div>
            <div>
                <h2 class="text-md font-bold tracking-widest text-[#d4af37] leading-none uppercase">LA TRIBU</h2>
                <span class="text-[10px] tracking-wider text-gray-400 font-medium">Sistema Restaurante</span>
            </div>
        </div>

        <!-- User Profile Info -->
        <div class="px-6 py-5 border-b border-white/5 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-[#d4af37]/10 border-2 border-[#d4af37] flex items-center justify-center text-[#d4af37] text-lg font-bold shadow-lg shadow-[#d4af37]/5 mb-2">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <h3 class="text-sm font-semibold text-white leading-none mb-1">{{ Auth::user()->name }}</h3>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Admin</span>
        </div>

        <!-- Sidebar Navigation Links -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.menu') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.menu') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Menú</span>
            </a>

            <a href="{{ route('admin.mesas') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.mesas') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>Mesas</span>
            </a>

            <a href="{{ route('admin.reservas') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.reservas') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Reservas</span>
            </a>

            <a href="{{ route('admin.pedidos') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.pedidos') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Pedidos</span>
            </a>

            <a href="{{ route('admin.usuarios') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.usuarios') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Usuarios</span>
            </a>

            <a href="{{ route('admin.reportes') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.reportes') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Reportes</span>
            </a>

            <a href="{{ route('admin.inventario') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold tracking-wider transition-all duration-200 {{ request()->routeIs('admin.inventario') ? 'bg-[#d4af37] text-[#121619] shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-[#d4af37]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Inventario</span>
            </a>
        </nav>
    </div>
</aside>
