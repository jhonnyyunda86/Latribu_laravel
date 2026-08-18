@php
    // La lista de mesas es provista por el controlador
@endphp

<x-mesero-layout>
    <x-slot name="header">
        {{ __('Mapa de Mesas y Estado de Salón') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para filtros y búsqueda de mesas -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            selectedMesaId: null,
            selectedMesaDetails: null,
            editModalOpen: false,
            editMesaId: null,
            editMesaNumero: '',
            editMesaEstado: '',
            mesas: {{ $mesas->map(fn($m) => [
                'id' => $m->id,
                'numero' => $m->numero,
                'capacidad' => $m->capacidad,
                'estado' => $m->estado,
                'ubicacion' => $m->ubicacion ?? 'Principal',
                'activo' => (bool)$m->activo,
                'pedido_activo' => $m->pedidos->first() ? [
                    'id' => $m->pedidos->first()->id,
                    'total' => number_format($m->pedidos->first()->total, 0, ',', '.'),
                    'observaciones' => $m->pedidos->first()->observaciones ?? 'Ninguna',
                    'items' => $m->pedidos->first()->detalles->map(fn($d) => [
                        'producto' => $d->producto->nombre ?? 'Desconocido',
                        'cantidad' => $d->cantidad,
                        'subtotal' => number_format($d->subtotal, 0, ',', '.')
                    ])
                ] : null
            ])->toJson() }},
            getFilteredMesas() {
                return this.mesas.filter(m => {
                    const matchesSearch = this.searchQuery === '' || 
                        m.numero.toString().includes(this.searchQuery) ||
                        m.ubicacion.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesEstado = this.selectedEstado === '' || m.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            },
            showDetails(mesa) {
                this.selectedMesaDetails = mesa;
            }
         }"
         class="space-y-6">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• DISTRIBUCIÓN EN VIVO •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Mapa de Mesas</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Supervisa la disponibilidad de mesas, solicita cuentas y administra el estado de los clientes en el salón.</p>
                </div>
                <div class="flex items-center gap-4 bg-white/5 border border-white/10 p-3.5 rounded-xl">
                    <div class="text-center px-2">
                        <span class="text-[9px] text-green-400 uppercase tracking-widest block font-bold leading-none mb-1">Disponibles</span>
                        <span class="text-lg font-bold font-mono text-green-400">{{ $mesas->where('estado', 'Disponible')->count() }}</span>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div class="text-center px-2">
                        <span class="text-[9px] text-red-400 uppercase tracking-widest block font-bold leading-none mb-1">Ocupadas</span>
                        <span class="text-lg font-bold font-mono text-red-400">{{ $mesas->where('estado', 'Ocupada')->count() }}</span>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div class="text-center px-2">
                        <span class="text-[9px] text-yellow-500 uppercase tracking-widest block font-bold leading-none mb-1">Cuentas</span>
                        <span class="text-lg font-bold font-mono text-yellow-500">{{ $mesas->where('estado', 'Cuenta')->count() }}</span>
                    </div>
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
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Estado Actualizado!</h4>
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

        <!-- Filtros y Buscador de Mesas -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="Buscar mesa por número o ubicación..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
            </div>

            <!-- Filtros de Estado -->
            <div class="flex items-center gap-2 overflow-x-auto shrink-0 pb-1 scrollbar-none">
                <button @click="selectedEstado = ''"
                        :class="selectedEstado === '' ? 'bg-[#121619] text-white' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Todas
                </button>
                <button @click="selectedEstado = 'Disponible'"
                        :class="selectedEstado === 'Disponible' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Disponible
                </button>
                <button @click="selectedEstado = 'Ocupada'"
                        :class="selectedEstado === 'Ocupada' ? 'bg-red-600 text-white border-red-600' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Ocupada
                </button>
                <button @click="selectedEstado = 'Reservada'"
                        :class="selectedEstado === 'Reservada' ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-[#FAF4EB] text-yellow-700 border border-yellow-200 hover:bg-yellow-100/60'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Reservada
                </button>
                <button @click="selectedEstado = 'Cuenta'"
                        :class="selectedEstado === 'Cuenta' ? 'bg-pink-600 text-white border-pink-600' : 'bg-pink-50 text-pink-700 border border-pink-200 hover:bg-pink-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Cuenta
                </button>
            </div>
        </div>

        <!-- Grid de Tarjetas de Mesas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <template x-for="mesa in getFilteredMesas()" :key="mesa.id">
                <div :class="{
                        'border-green-300 bg-green-50/10': mesa.estado === 'Disponible',
                        'border-red-300 bg-red-50/10': mesa.estado === 'Ocupada',
                        'border-yellow-300 bg-yellow-50/15': mesa.estado === 'Reservada',
                        'border-pink-300 bg-pink-50/10 border-dashed animate-pulse-slow': mesa.estado === 'Cuenta'
                     }"
                     class="bg-white border rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-4">
                    
                    <!-- Encabezado de la Tarjeta -->
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold block" x-text="mesa.ubicacion"></span>
                            <h4 class="text-xl font-bold font-serif text-[#2c1d11]" x-text="'Mesa ' + mesa.numero"></h4>
                        </div>
                        
                        <!-- Badge Estado -->
                        <span :class="{
                                'bg-green-100 text-green-800 border border-green-200': mesa.estado === 'Disponible',
                                'bg-red-100 text-red-800 border border-red-200': mesa.estado === 'Ocupada',
                                'bg-yellow-100 text-yellow-800 border border-yellow-200': mesa.estado === 'Reservada',
                                'bg-pink-100 text-pink-800 border border-pink-200': mesa.estado === 'Cuenta'
                              }"
                              class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full shadow-sm"
                              x-text="mesa.estado">
                        </span>
                    </div>

                    <!-- Datos Intermedios -->
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-semibold text-[#2c1d11]" x-text="mesa.capacidad + ' pers.'"></span>
                        </div>

                        <!-- Si está ocupada o cuenta, muestra el total de la orden -->
                        <template x-if="mesa.pedido_activo">
                            <div class="flex items-center gap-1.5 font-bold font-mono text-red-600">
                                <span>$</span>
                                <span x-text="mesa.pedido_activo.total"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Acciones rápidas según el estado -->
                    <div class="pt-3 border-t border-gray-100 flex flex-col gap-2">
                        
                        <!-- Si la mesa está disponible -->
                        <template x-if="mesa.estado === 'Disponible'">
                            <div class="flex flex-col gap-2 w-full">
                                <a :href="'/mesero/menu?mesa_id=' + mesa.id" class="w-full py-2 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-center text-[10px] uppercase tracking-wider transition shadow-sm block">
                                    Nueva Comanda
                                </a>
                                <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Ocupada">
                                    <button type="submit" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition">
                                        Marcar Ocupada
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Si la mesa está ocupada -->
                        <template x-if="mesa.estado === 'Ocupada'">
                            <div class="flex flex-col gap-2 w-full">
                                <button @click="showDetails(mesa)" class="w-full py-2 bg-[#121619] hover:bg-black text-white hover:text-[#d4af37] font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-[#d4af37]/20 flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver Consumo
                                </button>
                                <div class="grid grid-cols-2 gap-2">
                                    <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="Cuenta">
                                        <button type="submit" class="w-full py-2 bg-pink-50 hover:bg-pink-100 text-pink-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-pink-200">
                                            Pedir Cuenta
                                        </button>
                                    </form>
                                    <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="Disponible">
                                        <button type="submit" class="w-full py-2 bg-green-50 hover:bg-green-100 text-green-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-green-200">
                                            Liberar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>

                        <!-- Si la mesa está reservada -->
                        <template x-if="mesa.estado === 'Reservada'">
                            <div class="flex flex-col gap-2 w-full">
                                <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Ocupada">
                                    <button type="submit" class="w-full py-2 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-[10px] uppercase tracking-wider transition shadow-sm">
                                        Registrar Llegada
                                    </button>
                                </form>
                                <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Disponible">
                                    <button type="submit" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition">
                                        Cancelar Reserva
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Si la mesa está en estado de Cuenta -->
                        <template x-if="mesa.estado === 'Cuenta'">
                            <div class="flex flex-col gap-2 w-full">
                                <button @click="showDetails(mesa)" class="w-full py-2 bg-[#121619] hover:bg-black text-white hover:text-[#d4af37] font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-[#d4af37]/20 flex items-center justify-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Ver Detalle Cuenta
                                </button>
                                <form :action="'/mesero/mesas/' + mesa.id + '/status'" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Disponible">
                                    <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-[10px] uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Registrar Pago y Liberar
                                    </button>
                                </form>
                            </div>
                        </template>

                        <!-- Botón universal para cambiar estado -->
                        <button @click="editMesaId = mesa.id; editMesaNumero = mesa.numero; editMesaEstado = mesa.estado; editModalOpen = true" 
                                class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition flex items-center justify-center gap-1.5 border border-gray-200">
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
        <div x-show="getFilteredMesas().length === 0" class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm space-y-3">
            <div class="w-16 h-16 bg-[#FAF4EB] text-[#d4af37] rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="text-md font-bold text-[#2c1d11]">No se encontraron mesas</h4>
                <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Intenta modificando tu término de búsqueda o seleccionando otro filtro de estado.</p>
            </div>
        </div>

        <!-- MODAL DETALLE DE CONSUMO / CUENTA -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-show="selectedMesaDetails !== null"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <div class="bg-white rounded-2xl w-full max-w-md p-6 space-y-5 shadow-2xl relative"
                 @click.away="selectedMesaDetails = null"
                 x-show="selectedMesaDetails !== null"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="scale-95"
                 x-transition:enter-end="scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="scale-100"
                 x-transition:leave-end="scale-95">
                
                <!-- Botón Cerrar -->
                <button @click="selectedMesaDetails = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Encabezado Modal -->
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block mb-1">• CONSUMO ACTIVO •</span>
                    <h4 class="text-lg font-serif italic font-bold text-[#2c1d11]" x-text="'Detalles de Mesa ' + selectedMesaDetails?.numero"></h4>
                    <p class="text-[11px] text-gray-500 font-light">Resumen del pedido actual enviado a la cocina.</p>
                </div>

                <!-- Detalles del Pedido -->
                <div class="space-y-4">
                    <!-- Si hay items en el pedido activo -->
                    <template x-if="selectedMesaDetails?.pedido_activo">
                        <div class="space-y-3">
                            <div class="max-h-48 overflow-y-auto divide-y divide-gray-100 pr-1">
                                <template x-for="item in selectedMesaDetails.pedido_activo.items">
                                    <div class="py-2.5 flex items-center justify-between text-xs">
                                        <div class="flex-1">
                                            <p class="font-bold text-[#2c1d11]" x-text="item.producto"></p>
                                            <span class="text-[10px] text-gray-400" x-text="'Cantidad: ' + item.cantidad"></span>
                                        </div>
                                        <span class="font-mono font-bold text-gray-700" x-text="'$' + item.subtotal"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Observaciones -->
                            <div class="bg-gray-50 p-3 rounded-xl space-y-1">
                                <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold block">Notas del Pedido</span>
                                <p class="text-[11px] text-gray-600 font-light italic leading-relaxed" x-text="selectedMesaDetails.pedido_activo.observaciones"></p>
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between items-center bg-[#FAF4EB] p-3.5 rounded-xl border border-[#d4af37]/20">
                                <span class="text-xs uppercase tracking-wider font-extrabold text-[#2c1d11]">Total Consumo</span>
                                <span class="text-md font-bold font-mono text-[#2c1d11]" x-text="'$' + selectedMesaDetails.pedido_activo.total"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Si por alguna razón no tiene pedido activo asignado en la DB -->
                    <template x-if="!selectedMesaDetails?.pedido_activo">
                        <div class="py-6 text-center text-gray-400 space-y-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs font-light">No se encontró una comanda activa registrada para esta mesa.</p>
                        </div>
                    </template>
                </div>

                <!-- Botones pie del modal -->
                <div class="pt-2">
                    <button @click="selectedMesaDetails = null" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider transition">
                        Cerrar Detalles
                    </button>
                </div>

            </div>
        </div>

        <!-- MODAL CAMBIAR ESTADO DE MESA (Rol Mesero, similar al Admin) -->
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" />
                        </svg>
                        <span x-text="'Cambiar Estado - Mesa ' + editMesaNumero"></span>
                    </h4>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form :action="'/mesero/mesas/' + editMesaId + '/status'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-1">
                        <label class="font-bold text-[#2c1d11] block">Nuevo Estado de Mesa</label>
                        <select name="estado" x-model="editMesaEstado" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs font-semibold">
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                            <option value="Cuenta">Cuenta</option>
                            <option value="Mantenimiento">Mantenimiento</option>
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
