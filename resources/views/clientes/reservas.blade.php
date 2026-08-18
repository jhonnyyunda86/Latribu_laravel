@php
    // La lista de reservas y mesas disponibles es provista por el controlador
@endphp

<x-cliente-layout>
    <x-slot name="header">
        {{ __('Mis Reservas de Mesa') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros y Búsqueda -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            reservas: {{ $reservas->map(fn($r) => [
                'id' => $r->id,
                'mesa_numero' => $r->mesa?->numero ?? 'Por asignar',
                'fecha' => $r->fecha_reserva->format('d/m/Y'),
                'fecha_raw' => $r->fecha_reserva->format('Y-m-d'),
                'hora' => \Carbon\Carbon::parse($r->hora_reserva)->format('h:i A'),
                'personas' => $r->cantidad_personas,
                'estado' => $r->estado,
                'observaciones' => $r->observaciones ?? 'Ninguna'
            ])->toJson() }},
            getFilteredReservas() {
                return this.reservas.filter(r => {
                    const matchesSearch = this.searchQuery === '' || 
                        r.fecha.includes(this.searchQuery) ||
                        r.mesa_numero.toString().includes(this.searchQuery);
                    const matchesEstado = this.selectedEstado === '' || r.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            }
         }"
         class="space-y-6">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• RESERVA TU MESA •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Mis Reservaciones</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Organiza tus almuerzos y cenas en La Tribu. Elige tu mesa disponible favorita y agenda tu espacio con confirmación en línea.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ successModalOpen: true }" x-show="successModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
                <div class="bg-white rounded-2xl p-6 w-full max-w-sm border border-gray-200 shadow-2xl text-center space-y-4"
                     x-show="successModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                    
                    <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto border-2 border-green-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Reserva Creada!</h4>
                        <p class="text-xs text-gray-500 font-light leading-relaxed">{{ session('success') }}</p>
                    </div>

                    <div class="pt-2">
                        <button @click="successModalOpen = false" class="w-full py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- HISTORIAL DE RESERVAS (2/3 ancho) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Buscador y Filtros -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input x-model="searchQuery" 
                               type="text" 
                               placeholder="Buscar por fecha (dd/mm/aaaa) o número de mesa..." 
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
                    </div>

                    <!-- Filtros por estado -->
                    <div class="flex items-center gap-1.5 overflow-x-auto shrink-0 pb-1 scrollbar-none">
                        <button @click="selectedEstado = ''"
                                :class="selectedEstado === '' ? 'bg-[#121619] text-white' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                                class="px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider transition shrink-0">
                            Todas
                        </button>
                        <button @click="selectedEstado = 'Pendiente'"
                                :class="selectedEstado === 'Pendiente' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100'"
                                class="px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider transition shrink-0">
                            Pendientes
                        </button>
                        <button @click="selectedEstado = 'Confirmada'"
                                :class="selectedEstado === 'Confirmada' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'"
                                class="px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider transition shrink-0">
                            Confirmadas
                        </button>
                        <button @click="selectedEstado = 'Cancelada'"
                                :class="selectedEstado === 'Cancelada' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'"
                                class="px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider transition shrink-0">
                            Canceladas
                        </button>
                    </div>
                </div>

                <!-- Grid Reservas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <template x-for="reserva in getFilteredReservas()" :key="reserva.id">
                        <div :class="{
                                'border-amber-200 bg-amber-50/5': reserva.estado === 'Pendiente',
                                'border-green-200 bg-green-50/5': reserva.estado === 'Confirmada',
                                'border-gray-200 bg-white': reserva.estado === 'Cancelada'
                             }"
                             class="border rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4">
                            
                            <!-- Header Tarjeta -->
                            <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                                <div>
                                    <h4 class="text-sm font-bold text-[#2c1d11]" x-text="'Mesa #' + reserva.mesa_numero"></h4>
                                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold block" x-text="'Reserva #' + reserva.id"></span>
                                </div>
                                <span :class="{
                                        'bg-amber-100 text-amber-800 border-amber-200': reserva.estado === 'Pendiente',
                                        'bg-green-100 text-green-800 border-green-200': reserva.estado === 'Confirmada',
                                        'bg-red-100 text-red-800 border-red-200': reserva.estado === 'Cancelada'
                                      }"
                                      class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full shadow-sm"
                                      x-text="reserva.estado">
                                </span>
                            </div>

                            <!-- Datos de la reserva -->
                            <div class="grid grid-cols-3 gap-2 py-1 text-center bg-gray-50 rounded-xl border border-gray-100">
                                <div class="p-2 border-r border-gray-100">
                                    <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Fecha</span>
                                    <span class="text-xs font-bold text-[#2c1d11]" x-text="reserva.fecha"></span>
                                </div>
                                <div class="p-2 border-r border-gray-100">
                                    <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Hora</span>
                                    <span class="text-xs font-bold text-[#2c1d11]" x-text="reserva.hora"></span>
                                </div>
                                <div class="p-2">
                                    <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Invitados</span>
                                    <span class="text-xs font-bold text-[#2c1d11]" x-text="reserva.personas"></span>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="bg-yellow-50/20 border border-yellow-100/60 rounded-xl p-3 space-y-1">
                                <span class="text-[9px] uppercase tracking-wider text-yellow-600 font-bold block">Notas de Reserva</span>
                                <p class="text-[11px] text-gray-600 font-light italic leading-relaxed" x-text="reserva.observaciones"></p>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Estado Vacío -->
                <div x-show="getFilteredReservas().length === 0" class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm space-y-3">
                    <div class="w-16 h-16 bg-[#FAF4EB] text-[#d4af37] rounded-full flex items-center justify-center mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-md font-bold text-[#2c1d11]">No tienes reservas que coincidan</h4>
                        <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Puedes agendar una mesa usando el formulario de reserva rápida en el costado derecho.</p>
                    </div>
                </div>

            </div>

            <!-- FORMULARIO DE RESERVA RÁPIDA (1/3 ancho) -->
            <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-md font-serif italic text-[#2c1d11] font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservar Mesa
                    </h4>
                    <p class="text-xs text-gray-500 font-light mt-1">Elige los detalles para tu almuerzo o cena especial.</p>
                </div>

                <form action="{{ route('cliente.reservas.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- Selección de Mesa -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-500 uppercase tracking-widest block text-[9px]">Seleccionar Mesa Disponible *</label>
                        <select name="mesa_id" required 
                                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                            <option value="">Seleccione una mesa...</option>
                            @foreach($mesas as $mesa)
                                <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} (Capacidad: {{ $mesa->capacidad }} personas)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-500 uppercase tracking-widest block text-[9px]">Fecha de Reserva *</label>
                        <input type="date" name="fecha_reserva" required min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                    </div>

                    <!-- Hora -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-500 uppercase tracking-widest block text-[9px]">Hora de Reserva *</label>
                        <input type="time" name="hora_reserva" required 
                               class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                    </div>

                    <!-- Personas -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-500 uppercase tracking-widest block text-[9px]">Cantidad de Personas *</label>
                        <input type="number" name="cantidad_personas" required min="1" max="20" placeholder="Ej: 4"
                               class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                    </div>

                    <!-- Observaciones -->
                    <div class="space-y-1">
                        <label class="font-bold text-gray-500 uppercase tracking-widest block text-[9px]">Notas / Pedido Especial</label>
                        <textarea name="observaciones" placeholder="Ej: Cerca de la ventana, celebración de cumpleaños..."
                                  class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs h-20 focus:ring-[#d4af37] focus:border-[#d4af37] font-light leading-relaxed"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Agendar Reserva
                    </button>
                </form>
            </div>

        </div>

    </div>
</x-cliente-layout>
