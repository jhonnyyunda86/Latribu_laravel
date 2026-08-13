@php
    // Obtener los datos inyectados por el controlador
    // $pedidos
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Gestión de Pedidos') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros, Paginación Local e Historial de Pedidos -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            currentPage: 1,
            pedidos: {{ $pedidos->map(fn($p) => [
                'id' => $p->id,
                'user_name' => $p->user?->name ?? 'Cliente de paso',
                'mesa_numero' => $p->mesa?->numero ?? 'Llevar / Delivery',
                'mesero_name' => $p->mesero?->name ?? 'Cliente (Online)',
                'total' => $p->total,
                'estado' => $p->estado,
                'tipo_pedido' => $p->tipo_pedido ?? 'Mesa',
                'observaciones' => $p->observaciones ?? '',
                'fecha' => $p->created_at->format('Y-m-d H:i'),
                'detalles' => $p->detalles->map(fn($d) => [
                    'producto_nombre' => $d->producto?->nombre ?? 'Producto Eliminado',
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'subtotal' => $d->subtotal,
                    'observaciones' => $d->observaciones ?? ''
                ])
            ])->toJson() }},
            getFilteredPedidos() {
                return this.pedidos.filter(p => {
                    const matchesSearch = this.searchQuery === '' || p.user_name.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.mesa_numero.toString().includes(this.searchQuery) || p.mesero_name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesEstado = this.selectedEstado === '' || p.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            },
            getPaginatedPedidos() {
                const start = (this.currentPage - 1) * 6;
                return this.getFilteredPedidos().slice(start, start + 6);
            },
            totalPages() {
                return Math.ceil(this.getFilteredPedidos().length / 6) || 1;
            }
         }"
         x-init="$watch('searchQuery', () => currentPage = 1); $watch('selectedEstado', () => currentPage = 1);"
         class="space-y-6">
        
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• HISTORIAL DE COMANDAS •</span>
                <h3 class="text-2xl font-serif italic text-white">Monitoreo de Pedidos</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Revisa los pedidos tomados por los meseros y realizados por clientes. Modifica su estado a "Entregado" o "En Espera" en un clic.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Total Pedidos</span>
                <span class="text-2xl font-bold font-mono text-white">{{ $pedidos->count() }}</span>
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

        <!-- LISTADO DE PEDIDOS -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between min-h-[500px]">
            <div>
                <!-- Filtros y Cabecera -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Pedidos Recibidos</span>
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
                                   placeholder="Buscar por cliente, mesa o mesero...">
                        </div>

                        <select x-model="selectedEstado" 
                                class="px-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            <option value="">Todos los estados</option>
                            <option value="En Espera">En Espera</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de Pedidos -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                <th class="py-3">Mesa / Destino</th>
                                <th class="py-3">Cliente / Origen</th>
                                <th class="py-3">Tomado Por</th>
                                <th class="py-3">Fecha y Hora</th>
                                <th class="py-3">Total</th>
                                <th class="py-3">Estado</th>
                                <th class="py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs">
                            <template x-for="pedido in getPaginatedPedidos()" :key="pedido.id">
                                <!-- Cada pedido tiene su propio sub-estado Alpine para colapsar detalles -->
                                <tr x-data="{ detailsOpen: false }" class="hover:bg-gray-50/40 transition">
                                    <td class="py-3.5">
                                        <span class="font-bold text-[#2c1d11] block" x-text="pedido.mesa_numero"></span>
                                        <span class="text-[9px] px-1.5 py-0.2 bg-gray-100 text-gray-600 rounded uppercase tracking-wider font-semibold" x-text="pedido.tipo_pedido"></span>
                                    </td>
                                    <td class="py-3.5">
                                        <h5 class="font-bold text-[#2c1d11]" x-text="pedido.user_name"></h5>
                                        <span class="text-[9px] text-gray-400 block truncate max-w-xs" x-text="pedido.observaciones || 'Sin especificaciones'"></span>
                                    </td>
                                    <td class="py-3.5 text-gray-500 font-medium" x-text="pedido.mesero_name"></td>
                                    <td class="py-3.5 text-gray-400 font-mono" x-text="pedido.fecha"></td>
                                    <td class="py-3.5 font-bold font-mono text-[#2c1d11]" x-text="'$' + parseFloat(pedido.total).toFixed(2)"></td>
                                    <td class="py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                              :class="{
                                                  'bg-amber-50 text-[#d4af37]': pedido.estado === 'En Espera',
                                                  'bg-green-50 text-green-700': pedido.estado === 'Entregado'
                                              }"
                                              x-text="pedido.estado"></span>
                                    </td>
                                    <td class="py-3.5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1">
                                            <!-- Ver Productos / Detalle (Toggle) -->
                                            <button @click="detailsOpen = !detailsOpen" 
                                                    title="Ver Productos del Pedido" 
                                                    class="p-1.5 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-lg transition mr-1 flex items-center gap-1 font-semibold text-[10px] uppercase">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                                </svg>
                                                <span x-text="detailsOpen ? 'Cerrar' : 'Items'"></span>
                                            </button>

                                            <!-- Confirmar como Entregado -->
                                            <form method="POST" :action="'/admin/pedidos/' + pedido.id + '/status'" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="Entregado">
                                                <button type="submit" 
                                                        title="Marcar como Entregado" 
                                                        :disabled="pedido.estado === 'Entregado'"
                                                        :class="pedido.estado === 'Entregado' ? 'opacity-30 cursor-not-allowed' : 'bg-green-50 text-green-600 hover:bg-green-100'"
                                                        class="p-1.5 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Poner en Espera -->
                                            <form method="POST" :action="'/admin/pedidos/' + pedido.id + '/status'" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="En Espera">
                                                <button type="submit" 
                                                        title="Poner En Espera" 
                                                        :disabled="pedido.estado === 'En Espera'"
                                                        :class="pedido.estado === 'En Espera' ? 'opacity-30 cursor-not-allowed' : 'bg-amber-50 text-[#d4af37] hover:bg-amber-100'"
                                                        class="p-1.5 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <div class="w-[1px] h-4 bg-gray-100 mx-0.5"></div>

                                            <!-- Eliminar Pedido -->
                                            <form method="POST" :action="'/admin/pedidos/' + pedido.id" onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente este pedido?');" class="inline">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" title="Eliminar comanda" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Sub-Tabla Desplegable con los Productos del Pedido -->
                                <tr x-show="detailsOpen" x-cloak class="bg-[#fdfbf7]/50">
                                    <td colspan="7" class="px-6 py-4">
                                        <div class="border border-[#d4af37]/15 rounded-xl bg-white p-4 space-y-3">
                                            <h6 class="font-bold text-[#2c1d11] text-xs flex items-center gap-1.5 uppercase tracking-wider pb-1.5 border-b border-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                                <span>Detalle de Artículos Pedidos</span>
                                            </h6>
                                            <div class="grid grid-cols-1 divide-y divide-gray-50 text-xs">
                                                <template x-for="item in pedido.detalles" :key="item.producto_nombre">
                                                    <div class="py-2.5 flex justify-between items-center">
                                                        <div>
                                                            <span class="font-bold text-[#2c1d11]" x-text="item.producto_nombre"></span>
                                                            <span class="text-gray-400 font-medium" x-text="' x' + item.cantidad"></span>
                                                            <span class="text-[10px] text-gray-400 font-light block" x-text="item.observaciones || 'Sin especificaciones'"></span>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-bold text-[#2c1d11]" x-text="'$' + parseFloat(item.subtotal).toFixed(2)"></div>
                                                            <div class="text-[10px] text-gray-400 font-light" x-text="'c/u: $' + parseFloat(item.precio_unitario).toFixed(2)"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <template x-if="getFilteredPedidos().length === 0">
                        <div class="text-center py-12 text-gray-400">
                            <p class="text-xs">No se encontraron pedidos registrados.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Controles de Paginación Fijos abajo -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                <span class="text-[10px] text-gray-400 font-medium">
                    Mostrando página <span x-text="currentPage" class="font-bold text-[#2c1d11]"></span> de <span x-text="totalPages()" class="font-bold text-[#2c1d11]"></span> (<span x-text="getFilteredPedidos().length" class="font-bold text-[#2c1d11]"></span> pedidos filtrados)
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
