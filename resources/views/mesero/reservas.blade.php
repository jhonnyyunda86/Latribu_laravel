@php
    // La lista de reservas es provista por el controlador
@endphp

<x-mesero-layout>
    <x-slot name="header">
        {{ __('Control de Reservas de Clientes') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros y Búsqueda de Reservas -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            editModalOpen: false,
            editReservaId: null,
            editReservaCliente: '',
            editReservaEstado: '',
            reservas: {{ $reservas->map(fn($r) => [
                'id' => $r->id,
                'user_name' => $r->user?->name ?? 'Cliente General',
                'mesa_numero' => $r->mesa?->numero ?? 'Sin mesa asignada',
                'fecha' => $r->fecha_reserva->format('d/m/Y'),
                'hora' => \Carbon\Carbon::parse($r->hora_reserva)->format('h:i A'),
                'personas' => $r->cantidad_personas,
                'estado' => $r->estado,
                'observaciones' => $r->observaciones ?? 'Ninguna'
            ])->toJson() }},
            getFilteredReservas() {
                return this.reservas.filter(r => {
                    const matchesSearch = this.searchQuery === '' || 
                        r.user_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        r.mesa_numero.toString().includes(this.searchQuery) ||
                        r.fecha.includes(this.searchQuery);
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
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• AGENDAMIENTO •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Reservaciones</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Supervisa las reservaciones del día. Confirma o cancela las solicitudes de tus clientes para optimizar el espacio de las mesas.</p>
                </div>
                <div class="text-right bg-white/5 border border-white/10 p-3 rounded-xl min-w-[120px] text-center">
                    <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold mb-0.5">Total Reservas</span>
                    <span class="text-xl font-bold font-mono text-white">{{ $reservas->count() }}</span>
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
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Reserva Actualizada!</h4>
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

        <!-- Barra de Búsqueda y Filtros de Estado -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="Buscar por cliente, mesa o fecha (dd/mm/aaaa)..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
            </div>

            <!-- Filtros de Estado -->
            <div class="flex items-center gap-2 overflow-x-auto shrink-0 pb-1 scrollbar-none">
                <button @click="selectedEstado = ''"
                        :class="selectedEstado === '' ? 'bg-[#121619] text-white' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Todas
                </button>
                <button @click="selectedEstado = 'Pendiente'"
                        :class="selectedEstado === 'Pendiente' ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Pendientes
                </button>
                <button @click="selectedEstado = 'Confirmada'"
                        :class="selectedEstado === 'Confirmada' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Confirmadas
                </button>
                <button @click="selectedEstado = 'Cancelada'"
                        :class="selectedEstado === 'Cancelada' ? 'bg-red-600 text-white border-red-600' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Canceladas
                </button>
            </div>
        </div>

        <!-- Grid de Tarjetas de Reservas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="reserva in getFilteredReservas()" :key="reserva.id">
                <div :class="{
                        'border-amber-200 bg-amber-50/5': reserva.estado === 'Pendiente',
                        'border-green-200 bg-green-50/5': reserva.estado === 'Confirmada',
                        'border-gray-200 bg-white': reserva.estado === 'Cancelada'
                     }"
                     class="border rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-4">
                    
                    <!-- Encabezado Tarjeta -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h4 class="text-md font-bold font-serif text-[#2c1d11]" x-text="reserva.user_name"></h4>
                            <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold block" x-text="'Mesa ' + reserva.mesa_numero"></span>
                        </div>
                        
                        <!-- Badge Estado -->
                        <span :class="{
                                'bg-amber-100 text-amber-800 border border-amber-200': reserva.estado === 'Pendiente',
                                'bg-green-100 text-green-800 border border-green-200': reserva.estado === 'Confirmada',
                                'bg-red-100 text-red-800 border border-red-200': reserva.estado === 'Cancelada'
                              }"
                              class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full shadow-sm"
                              x-text="reserva.estado">
                        </span>
                    </div>

                    <!-- Datos Intermedios: Fecha, Hora, Personas -->
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
                            <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Personas</span>
                            <span class="text-xs font-bold text-[#2c1d11]" x-text="reserva.personas"></span>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="bg-yellow-50/20 border border-yellow-100/60 rounded-xl p-3 space-y-1">
                        <span class="text-[9px] uppercase tracking-wider text-yellow-600 font-bold block">Notas / Requerimientos</span>
                        <p class="text-[11px] text-gray-600 font-light italic leading-relaxed" x-text="reserva.observaciones"></p>
                    </div>

                    <!-- Acciones del Pedido -->
                    <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                        
                        <!-- Si está Pendiente -->
                        <template x-if="reserva.estado === 'Pendiente'">
                            <div class="flex items-center gap-2 w-full">
                                <form :action="'/mesero/reservas/' + reserva.id + '/status'" method="POST" class="w-1/2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Confirmada">
                                    <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-[10px] uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Confirmar
                                    </button>
                                </form>
                                <form :action="'/mesero/reservas/' + reserva.id + '/status'" method="POST" class="w-1/2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Cancelada">
                                    <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-red-200">
                                        Cancelar
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Si está Confirmada -->
                        <template x-if="reserva.estado === 'Confirmada'">
                            <div class="flex items-center gap-2 w-full">
                                <form :action="'/mesero/reservas/' + reserva.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Cancelada">
                                    <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-red-200">
                                        Cancelar Reserva
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Si está Cancelada -->
                        <template x-if="reserva.estado === 'Cancelada'">
                            <div class="flex items-center gap-2 w-full">
                                <form :action="'/mesero/reservas/' + reserva.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Confirmada">
                                    <button type="submit" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-gray-200">
                                        Re-Confirmar
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Botón universal para cambiar estado -->
                        <button @click="editReservaId = reserva.id; editReservaCliente = reserva.user_name; editReservaEstado = reserva.estado; editModalOpen = true" 
                                class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition flex items-center justify-center gap-1.5 border border-gray-200 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Modificar Estado
                        </button>
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
                <h4 class="text-md font-bold text-[#2c1d11]">No se encontraron reservas</h4>
                <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Intenta modificando tu término de búsqueda o seleccionando otro filtro de estado.</p>
            </div>
        </div>

        <!-- MODAL CAMBIAR ESTADO DE RESERVA (Rol Mesero, similar al Admin) -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <div class="bg-white rounded-2xl w-full max-w-sm p-6 space-y-4 shadow-2xl relative"
                 @click.away="editModalOpen = false"
                 x-show="editModalOpen"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="scale-95"
                 x-transition:enter-end="scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="scale-100"
                 x-transition:leave-end="scale-95">
                
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-text="'Editar Estado - Reserva #' + editReservaId"></span>
                    </h4>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form :action="'/mesero/reservas/' + editReservaId + '/status'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-1">
                        <label class="font-bold text-[#2c1d11] block" x-text="'Cliente: ' + editReservaCliente"></label>
                        <select name="estado" x-model="editReservaEstado" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Confirmada">Confirmada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" @click="editModalOpen = false" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition">
                            Cancelar
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-mesero-layout>
