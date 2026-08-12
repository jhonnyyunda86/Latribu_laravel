<x-mesero-layout>
    <x-slot name="header">
        {{ __('Panel de Control de Meseros') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Tarjeta de Bienvenida -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• ATENCIÓN Y SERVICIO •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">¡Hola de nuevo, {{ Auth::user()->name }}!</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Accede a tu zona de servicio de La Tribu. Monitorea el estado de las mesas a tu cargo y gestiona las comandas directamente a cocina.</p>
                </div>
                <div class="flex items-center gap-3 bg-white/5 backdrop-blur-sm p-3.5 rounded-xl border border-white/10">
                    <span class="text-xs font-semibold text-[#d4af37] uppercase tracking-wider">Turno Activo</span>
                </div>
            </div>
        </div>

        <!-- Grid de Métricas de Servicio -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Mesas Asignadas -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Mesas a Cargo</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">4 Mesas</h4>
                    <span class="text-[10px] text-gray-400 block">Mesas: 3, 5, 7 y 8</span>
                </div>
                <div class="w-12 h-12 bg-[#FAF4EB] text-[#d4af37] rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Pedidos Activos -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Mis Pedidos</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">3 Activos</h4>
                    <span class="text-[10px] text-yellow-600 font-semibold flex items-center gap-1">
                        2 en preparación / 1 listo
                    </span>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>

            <!-- Notificación de Cocina -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Por Entregar</span>
                    <h4 class="text-2xl font-bold tracking-tight text-green-600">1 Pedido Listo</h4>
                    <span class="text-[10px] text-green-500 font-semibold">
                        Llevar a Mesa 7
                    </span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center animate-bounce">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Servicios Completados -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Servicios Hoy</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">14 Entregas</h4>
                    <span class="text-[10px] text-[#d4af37] font-semibold">
                        Calificación: 4.9 &starf;
                    </span>
                </div>
                <div class="w-12 h-12 bg-[#FAF4EB] text-[#d4af37] rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Mesas Asignadas y Estado -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 lg:col-span-2 space-y-4">
                <h4 class="text-md font-serif italic text-[#2c1d11] font-semibold border-b border-gray-100 pb-3">Estado de mis Mesas</h4>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl border border-gray-200 text-center space-y-2 bg-green-50/50">
                        <span class="text-xs font-bold text-green-700 block">Mesa 3</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider bg-green-200 text-green-800 px-2 py-0.5 rounded-full">Disponible</span>
                        <p class="text-[10px] text-gray-400">Capacidad: 4</p>
                    </div>
                    <div class="p-4 rounded-xl border border-gray-200 text-center space-y-2 bg-red-50/50">
                        <span class="text-xs font-bold text-red-700 block">Mesa 5</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider bg-red-200 text-red-800 px-2 py-0.5 rounded-full">Ocupada</span>
                        <p class="text-[10px] text-gray-400">Capacidad: 2</p>
                    </div>
                    <div class="p-4 rounded-xl border border-gray-200 text-center space-y-2 bg-[#FAF4EB]/60">
                        <span class="text-xs font-bold text-[#d4af37] block">Mesa 7</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider bg-[#d4af37]/20 text-[#2c1d11] px-2 py-0.5 rounded-full">Reservada</span>
                        <p class="text-[10px] text-gray-400">Capacidad: 6</p>
                    </div>
                    <div class="p-4 rounded-xl border border-gray-200 text-center space-y-2 bg-red-50/50">
                        <span class="text-xs font-bold text-red-700 block">Mesa 8</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider bg-red-200 text-red-800 px-2 py-0.5 rounded-full">Cuenta</span>
                        <p class="text-[10px] text-gray-400">Capacidad: 4</p>
                    </div>
                </div>
            </div>

            <!-- Accesos directos de servicio -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
                <div class="space-y-3">
                    <h4 class="text-md font-serif italic text-[#2c1d11] font-semibold border-b border-gray-100 pb-3">Servicio Rápido</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Usa estas herramientas para notificar a caja, abrir mesas o pedir asistencia directa al administrador de turno.</p>
                </div>
                <div class="pt-4 space-y-2">
                    <button class="w-full py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                        Nueva Comanda
                    </button>
                    <button class="w-full py-2.5 bg-[#121619] hover:bg-[#1d2226] text-white font-bold rounded-xl text-xs uppercase tracking-wider transition border border-[#d4af37]/20">
                        Solicitar Soporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-mesero-layout>
