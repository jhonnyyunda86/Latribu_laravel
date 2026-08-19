@php
    // La lista de pedidos es provista por el controlador
@endphp

<x-cliente-layout>
    <x-slot name="header">
        {{ __('Historial de Mis Pedidos') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros, Búsqueda y Detalles -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            selectedPedido: null,
            pedidos: {{ $pedidos->map(fn($p) => [
                'id' => $p->id,
                'tipo' => ucfirst($p->tipo_pedido),
                'total' => number_format($p->total, 0, ',', '.'),
                'estado' => $p->estado,
                'fecha' => $p->created_at->format('d/m/Y H:i'),
                'observaciones' => $p->observaciones ?? 'Sin observaciones.',
                'factura_id' => $p->factura?->id ?? null,
                'detalles' => $p->detalles->map(fn($d) => [
                    'producto' => $d->producto?->nombre ?? 'Producto Desconocido',
                    'cantidad' => $d->cantidad,
                    'precio' => number_format($d->precio_unitario, 0, ',', '.'),
                    'subtotal' => number_format($d->subtotal, 0, ',', '.')
                ])
            ])->toJson() }},
            getFilteredPedidos() {
                return this.pedidos.filter(p => {
                    const matchesSearch = this.searchQuery === '' || 
                        p.id.toString().includes(this.searchQuery) ||
                        p.fecha.includes(this.searchQuery) ||
                        p.observaciones.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesEstado = this.selectedEstado === '' || p.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            },
            showDetails(pedido) {
                this.selectedPedido = pedido;
            }
         }"
         class="space-y-6">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• MIS COMPRAS •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Mis Pedidos</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Realiza un seguimiento de tus pedidos a domicilio o tus consumos presenciales en nuestro restaurante familiar.</p>
                </div>
                <div class="text-right bg-white/5 border border-white/10 p-3 rounded-xl min-w-[120px] text-center">
                    <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold mb-0.5">Pedidos Realizados</span>
                    <span class="text-xl font-bold font-mono text-white">{{ $pedidos->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda y Filtros de Estado -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="Buscar por número de pedido, fecha o indicaciones..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
            </div>

            <!-- Filtros de Estado -->
            <div class="flex items-center gap-2 overflow-x-auto shrink-0 pb-1 scrollbar-none">
                <button @click="selectedEstado = ''"
                        :class="selectedEstado === '' ? 'bg-[#121619] text-white' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Todos
                </button>
                <button @click="selectedEstado = 'En Espera'"
                        :class="selectedEstado === 'En Espera' ? 'bg-orange-500 text-white' : 'bg-orange-55 text-orange-700 border border-orange-200 hover:bg-orange-100'"
                        class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    En Espera
                </button>
                <button @click="selectedEstado = 'Entregado'"
                        :class="selectedEstado === 'Entregado' ? 'bg-green-600 text-white' : 'bg-green-55 text-green-700 border border-green-200 hover:bg-green-100'"
                        class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Entregados
                </button>
            </div>
        </div>

        <!-- Grid de Tarjetas de Pedidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="pedido in getFilteredPedidos()" :key="pedido.id">
                <div :class="{
                        'border-orange-200 bg-orange-50/5': pedido.estado === 'En Espera',
                        'border-green-200 bg-green-50/5': pedido.estado === 'Entregado',
                        'border-gray-200 bg-white': pedido.estado !== 'En Espera' && pedido.estado !== 'Entregado'
                     }"
                     class="border rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-4">
                    
                    <!-- Encabezado Tarjeta -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block" x-text="pedido.tipo"></span>
                            <h4 class="text-md font-bold text-[#2c1d11]" x-text="'Pedido #' + pedido.id"></h4>
                            <span class="text-[10px] text-gray-400 font-light" x-text="pedido.fecha"></span>
                        </div>
                        
                        <!-- Badge Estado -->
                        <span :class="{
                                'bg-orange-100 text-orange-850 border-orange-200': pedido.estado === 'En Espera',
                                'bg-green-100 text-green-850 border-green-200': pedido.estado === 'Entregado',
                                'bg-gray-100 text-gray-800 border-gray-200': pedido.estado !== 'En Espera' && pedido.estado !== 'Entregado'
                              }"
                              class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full border shadow-sm"
                              x-text="pedido.estado">
                        </span>
                    </div>

                    <!-- Detalles Generales -->
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div>
                            <span class="text-[10px] text-gray-400 block uppercase tracking-wider font-bold">Entrega / Comentarios</span>
                            <p class="font-medium text-[#2c1d11] line-clamp-2" x-text="pedido.observaciones"></p>
                        </div>
                        <div class="flex justify-between text-[#2c1d11] font-bold text-sm bg-gray-50 p-2.5 rounded-xl border border-gray-100 mt-2">
                            <span>Total Pedido:</span>
                            <span class="font-mono" x-text="'$' + pedido.total"></span>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-2 flex items-center gap-2">
                        <button @click="showDetails(pedido)" class="w-1/2 py-2 bg-[#121619] hover:bg-black text-white hover:text-[#d4af37] font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-[#d4af37]/20 flex items-center justify-center gap-1 shadow-sm">
                            Ver Compra
                        </button>
                        
                        <!-- Si el pedido tiene factura asociada -->
                        <template x-if="pedido.factura_id !== null">
                            <a :href="'/cliente/facturas/' + pedido.factura_id + '/pdf'" class="w-1/2 py-2 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-[10px] uppercase tracking-wider transition text-center shadow-sm flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Ticket POS
                            </a>
                        </template>
                        <template x-if="pedido.factura_id === null">
                            <button disabled class="w-1/2 py-2 bg-gray-100 text-gray-400 font-bold rounded-xl text-[10px] uppercase tracking-wider transition text-center border border-gray-200 cursor-not-allowed">
                                Sin Factura
                            </button>
                        </template>
                    </div>

                </div>
            </template>
        </div>

        <!-- Estado Vacío -->
        <div x-show="getFilteredPedidos().length === 0" class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm space-y-3">
            <div class="w-16 h-16 bg-[#FAF4EB] text-[#d4af37] rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="text-md font-bold text-[#2c1d11]">No se encontraron pedidos</h4>
                <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Tus órdenes aparecerán aquí cuando compres a domicilio o consumas en el salón.</p>
            </div>
        </div>

        <!-- MODAL DETALLE DE COMPRA / PEDIDO -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-show="selectedPedido !== null"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <div class="bg-white rounded-2xl w-full max-w-md p-6 space-y-5 shadow-2xl relative"
                 @click.away="selectedPedido = null"
                 x-show="selectedPedido !== null"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="scale-95"
                 x-transition:enter-end="scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="scale-100"
                 x-transition:leave-end="scale-95">
                
                <!-- Botón Cerrar -->
                <button @click="selectedPedido = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Encabezado Modal -->
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block mb-1" x-text="selectedPedido?.tipo"></span>
                    <h4 class="text-lg font-serif italic font-bold text-[#2c1d11]" x-text="'Detalle de Pedido #' + selectedPedido?.id"></h4>
                    <p class="text-[11px] text-gray-500 font-light" x-text="selectedPedido?.fecha"></p>
                </div>

                <!-- Detalles de Productos -->
                <div class="space-y-4">
                    <div class="max-h-48 overflow-y-auto divide-y divide-gray-100 pr-1">
                        <template x-for="item in selectedPedido?.detalles">
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div class="flex-1 pr-2">
                                    <p class="font-bold text-[#2c1d11]" x-text="item.producto"></p>
                                    <span class="text-[10px] text-gray-400" x-text="item.cantidad + ' x $' + item.precio"></span>
                                </div>
                                <span class="font-mono font-bold text-gray-700" x-text="'$' + item.subtotal"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Resumen del Pago -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs space-y-1">
                        <div class="flex justify-between text-gray-500">
                            <span>Estado de Preparación:</span>
                            <span class="font-bold text-[#2c1d11]" x-text="selectedPedido?.estado"></span>
                        </div>
                        <div class="border-t border-gray-200 my-1 pt-1.5 flex justify-between font-bold text-[#2c1d11] text-sm">
                            <span>Total</span>
                            <span class="font-mono" x-text="'$' + selectedPedido?.total"></span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Cierre y PDF en Modal -->
                <div class="pt-2 flex items-center gap-3">
                    <button @click="selectedPedido = null" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider transition">
                        Cerrar
                    </button>
                    
                    <template x-if="selectedPedido?.factura_id !== null">
                        <a :href="'/cliente/facturas/' + selectedPedido?.factura_id + '/pdf'" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider text-center transition shadow-sm flex items-center justify-center gap-1.5 font-sans">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Descargar POS
                        </a>
                    </template>
                    <template x-if="selectedPedido?.factura_id === null">
                        <button disabled class="w-1/2 py-2.5 bg-gray-100 text-gray-400 font-bold rounded-xl text-xs uppercase tracking-wider cursor-not-allowed border border-gray-200">
                            Sin Factura
                        </button>
                    </template>
                </div>

            </div>
        </div>

    </div>
</x-cliente-layout>
