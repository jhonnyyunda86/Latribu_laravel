<x-cliente-layout>
    <x-slot name="header">
        {{ __('Mi Portal de Cliente') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Tarjeta de Bienvenida Estilo Vintage La Tribu -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• LA RECETA ORIGINAL •</span>
                    <h3 class="text-3xl font-serif tracking-wide italic text-white mb-2">¡Bienvenido a La Tribu, {{ Auth::user()->name }}!</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light leading-relaxed">Disfruta de la mejor experiencia gastronómica familiar en nuestro salón. Reserva tu mesa favorita de forma fácil, mantente al día con tus beneficios y realiza tus pedidos favoritos.</p>
                </div>
                
                <div class="flex items-center gap-4 bg-white/5 backdrop-blur-sm p-4 rounded-xl border border-white/10 shrink-0">
                    <div class="text-right">
                        <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Mis Puntos La Tribu</span>
                        <span class="text-lg font-bold text-white font-mono">1,250 Pts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Información en Colores Crema y Marrón Vintage -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Reservas Activas -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Reservas Activas</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">1 Activa</h4>
                    <span class="text-[10px] text-[#d4af37] font-semibold">Hoy a las 8:30 PM</span>
                </div>
                <div class="w-12 h-12 bg-[#FAF4EB] text-[#d4af37] rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Pedidos Realizados -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Pedidos Totales</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">6 Realizados</h4>
                    <span class="text-[10px] text-gray-400 block">Último: hace 3 días</span>
                </div>
                <div class="w-12 h-12 bg-[#FAF4EB] text-orange-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>

            <!-- Cupón Especial -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Cupón Activo</span>
                    <h4 class="text-2xl font-bold tracking-tight text-green-700">15% OFF</h4>
                    <span class="text-[10px] text-green-600 font-medium">Cód: TRIBUCLUB</span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Club de Fidelidad -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center justify-between hover:shadow-md transition">
                <div class="space-y-1">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Categoría</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#d4af37]">Socio Gold</h4>
                    <span class="text-[10px] text-gray-400">La Tribu Privilege</span>
                </div>
                <div class="w-12 h-12 bg-[#FAF4EB] text-[#d4af37] rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Secciones de Contenido -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Historial de Reservas -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 lg:col-span-2 space-y-4">
                <h4 class="text-lg font-serif italic text-[#2c1d11] font-semibold border-b border-gray-100 pb-3">Mi Historial de Reservas</h4>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-[#fdfbf7] rounded-xl border border-gray-100">
                        <div>
                            <h5 class="text-sm font-semibold text-[#2c1d11]">Reserva de Mesa Especial</h5>
                            <p class="text-[11px] text-gray-500">Hoy a las 8:30 PM • Mesa 4 • 4 Personas</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-[#FAF4EB] text-[#d4af37] uppercase tracking-wider">Confirmada</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-[#fdfbf7] rounded-xl border border-gray-100">
                        <div>
                            <h5 class="text-sm font-semibold text-[#2c1d11]">Almuerzo Familiar</h5>
                            <p class="text-[11px] text-gray-500">Hace 1 semana • Mesa Principal • 6 Personas</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-green-50 text-green-800 uppercase tracking-wider">Completada</span>
                    </div>
                </div>
            </div>

            <!-- Reservas Rápidas -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <h4 class="text-lg font-serif italic text-[#2c1d11] font-semibold border-b border-gray-100 pb-3">Reserva una Mesa</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">¿Planeas una cena romántica o una comida especial? Asegura tu mesa favorita de forma rápida con confirmación instantánea.</p>
                </div>
                <div class="pt-4">
                    <button class="w-full py-3 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                        Reservar Mesa Ahora
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-cliente-layout>
