@php
    // Obtener las reservas inyectadas por el controlador
    // $reservas
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Gestión de Reservas') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros y Paginación Local de Reservas -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            currentPage: 1,
            reservas: {{ $reservas->map(fn($r) => [
                'id' => $r->id,
                'user_name' => $r->user?->name ?? 'Usuario no encontrado',
                'mesa_numero' => $r->mesa?->numero ?? 'N/A',
                'fecha_reserva' => $r->fecha_reserva->format('Y-m-d'),
                'hora_reserva' => $r->hora_reserva,
                'cantidad_personas' => $r->cantidad_personas,
                'estado' => $r->estado,
                'observaciones' => $r->observaciones ?? ''
            ])->toJson() }},
            getFilteredReservas() {
                return this.reservas.filter(r => {
                    const matchesSearch = this.searchQuery === '' || r.user_name.toLowerCase().includes(this.searchQuery.toLowerCase()) || r.mesa_numero.toString().includes(this.searchQuery) || r.fecha_reserva.includes(this.searchQuery);
                    const matchesEstado = this.selectedEstado === '' || r.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            },
            getPaginatedReservas() {
                const start = (this.currentPage - 1) * 6;
                return this.getFilteredReservas().slice(start, start + 6);
            },
            totalPages() {
                return Math.ceil(this.getFilteredReservas().length / 6) || 1;
            }
         }"
         x-init="$watch('searchQuery', () => currentPage = 1); $watch('selectedEstado', () => currentPage = 1);"
         class="space-y-6">
        
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• CONTROL DE RESERVACIONES •</span>
                <h3 class="text-2xl font-serif italic text-white">Libro de Reservas</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Monitorea las reservas hechas por tus meseros o clientes. Puedes confirmar, cancelar o poner en espera cada reservación.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Total Reservas</span>
                <span class="text-2xl font-bold font-mono text-white">{{ $reservas->count() }}</span>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ successModalOpen: true }" x-show="successModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
                <div class="bg-white rounded-2xl p-6 w-full max-w-sm border border-gray-200 shadow-2xl text-center space-y-4"
                     x-show="successModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                    
                    <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto border-2 border-green-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Operación Completada!</h4>
                        <p class="text-xs text-gray-500 font-light leading-relaxed">{{ session('success') }}</p>
                    </div>

                    <div class="pt-2">
                        <button @click="successModalOpen = false" class="w-full py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- LISTADO DE RESERVAS (Ancho Completo) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between min-h-[500px]">
            <div>
                <!-- Filtros y Cabecera -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Reservas Recibidas</span>
                    </h4>

                    <!-- Filtros -->
                    <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full md:max-w-md">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="searchQuery" 
                                   class="w-full pl-9 pr-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" 
                                   placeholder="Buscar por cliente, mesa o fecha...">
                        </div>

                        <select x-model="selectedEstado" 
                                class="px-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Confirmada">Confirmada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de Reservas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                        <th class="py-3">Cliente</th>
                                        <th class="py-3">Mesa</th>
                                        <th class="py-3">Fecha y Hora</th>
                                        <th class="py-3">Personas</th>
                                        <th class="py-3">Estado</th>
                                        <th class="py-3 text-right">Acciones de Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-xs">
                                    <template x-for="reserva in getPaginatedReservas()" :key="reserva.id">
                                        <tr>
                                            <td class="py-3.5 pr-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full bg-[#FAF4EB] text-[#d4af37] flex items-center justify-center font-bold text-xs shrink-0 border border-gray-100">
                                                        <span x-text="reserva.user_name.substring(0,2).toUpperCase()"></span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-[#2c1d11]" x-text="reserva.user_name"></h5>
                                                        <span class="text-[10px] text-gray-400 font-light truncate max-w-xs block" x-text="reserva.observaciones || 'Sin observaciones'"></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5">
                                                <span class="font-bold text-[#2c1d11]" x-text="'Mesa ' + reserva.mesa_numero"></span>
                                            </td>
                                            <td class="py-3.5 text-gray-500 font-medium">
                                                <div x-text="reserva.fecha_reserva"></div>
                                                <div class="text-[10px] text-gray-400 font-mono" x-text="reserva.hora_reserva"></div>
                                            </td>
                                            <td class="py-3.5 font-bold font-mono text-[#2c1d11]" x-text="reserva.cantidad_personas + ' personas'"></td>
                                            <td class="py-3.5">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                                      :class="{
                                                          'bg-amber-50 text-[#d4af37]': reserva.estado === 'Pendiente',
                                                          'bg-green-50 text-green-700': reserva.estado === 'Confirmada',
                                                          'bg-red-50 text-red-600': reserva.estado === 'Cancelada'
                                                      }"
                                                      x-text="reserva.estado"></span>
                                            </td>
                                            <td class="py-3.5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <!-- Confirmar -->
                                                    <form method="POST" :action="'/admin/reservas/' + reserva.id + '/status'" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="estado" value="Confirmada">
                                                        <button type="submit" 
                                                                title="Confirmar Reserva" 
                                                                :disabled="reserva.estado === 'Confirmada'"
                                                                :class="reserva.estado === 'Confirmada' ? 'opacity-30 cursor-not-allowed' : 'bg-green-50 text-green-600 hover:bg-green-100'"
                                                                class="p-1.5 rounded-lg transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <!-- En Espera / Pendiente -->
                                                    <form method="POST" :action="'/admin/reservas/' + reserva.id + '/status'" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="estado" value="Pendiente">
                                                        <button type="submit" 
                                                                title="Poner en Espera" 
                                                                :disabled="reserva.estado === 'Pendiente'"
                                                                :class="reserva.estado === 'Pendiente' ? 'opacity-30 cursor-not-allowed' : 'bg-amber-50 text-[#d4af37] hover:bg-amber-100'"
                                                                class="p-1.5 rounded-lg transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <!-- Cancelar -->
                                                    <form method="POST" :action="'/admin/reservas/' + reserva.id + '/status'" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="estado" value="Cancelada">
                                                        <button type="submit" 
                                                                title="Cancelar Reserva" 
                                                                :disabled="reserva.estado === 'Cancelada'"
                                                                :class="reserva.estado === 'Cancelada' ? 'opacity-30 cursor-not-allowed' : 'bg-red-50 text-red-600 hover:bg-red-100'"
                                                                class="p-1.5 rounded-lg transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <div class="w-[1px] h-4 bg-gray-200 mx-1"></div>

                                                    <!-- Eliminar completamente de la DB -->
                                                    <form method="POST" :action="'/admin/reservas/' + reserva.id" onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente esta reserva?');" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" title="Eliminar registro" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            
                            <template x-if="getFilteredReservas().length === 0">
                                <div class="text-center py-12 text-gray-400">
                                    <p class="text-xs">No se encontraron reservas registradas.</p>
                                </div>
                            </template>
                        </div>
            </div>

            <!-- Controles de Paginación Fijos abajo -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                <span class="text-[10px] text-gray-400 font-medium">
                    Mostrando página <span x-text="currentPage" class="font-bold text-[#2c1d11]"></span> de <span x-text="totalPages()" class="font-bold text-[#2c1d11]"></span> (<span x-text="getFilteredReservas().length" class="font-bold text-[#2c1d11]"></span> reservas filtradas)
                </span>

                <div class="flex items-center gap-2">
                    <button @click="currentPage > 1 ? currentPage-- : null" 
                            :disabled="currentPage === 1" 
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#121619]'"
                            class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-bold uppercase transition">
                        Anterior
                    </button>
                    <button @click="currentPage < totalPages() ? currentPage++ : null" 
                            :disabled="currentPage === totalPages()" 
                            :class="currentPage === totalPages() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#121619]'"
                            class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-bold uppercase transition">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
