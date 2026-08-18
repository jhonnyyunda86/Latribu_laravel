@php
    // La lista de pedidos es provista por el controlador
@endphp

<x-mesero-layout>
    <x-slot name="header">
        {{ __('Monitoreo y Entrega de Pedidos') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para Filtros y Búsqueda de Pedidos -->
    <div x-data="{ 
            searchQuery: '', 
            selectedEstado: '',
            pedidos: {{ $pedidos->map(fn($p) => [
                'id' => $p->id,
                'user_name' => $p->user?->name ?? 'Cliente de paso',
                'mesa_numero' => $p->mesa?->numero ?? 'Llevar / Delivery',
                'mesero_name' => $p->mesero?->name ?? 'Cliente (Online)',
                'total' => number_format($p->total, 0, ',', '.'),
                'estado' => $p->estado,
                'tipo_pedido' => $p->tipo_pedido ?? 'Mesa',
                'observaciones' => $p->observaciones ?? 'Ninguna',
                'fecha' => $p->created_at->format('d/m/Y H:i'),
                'detalles' => $p->detalles->map(fn($d) => [
                    'producto_nombre' => $d->producto?->nombre ?? 'Producto Eliminado',
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => number_format($d->precio_unitario, 0, ',', '.'),
                    'subtotal' => number_format($d->subtotal, 0, ',', '.')
                ])
            ])->toJson() }},
            getFilteredPedidos() {
                return this.pedidos.filter(p => {
                    const matchesSearch = this.searchQuery === '' || 
                        p.id.toString().includes(this.searchQuery) ||
                        p.user_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.mesa_numero.toString().includes(this.searchQuery) ||
                        p.mesero_name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesEstado = this.selectedEstado === '' || p.estado === this.selectedEstado;
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
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• SEGUIMIENTO DE COCINA •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Control de Pedidos</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Monitorea las órdenes de tus mesas y los pedidos online. Cambia el estado a "Entregado" una vez que sirvas el platillo en la mesa.</p>
                </div>
                <div class="text-right bg-white/5 border border-white/10 p-3 rounded-xl min-w-[120px] text-center">
                    <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold mb-0.5">Total Registrados</span>
                    <span class="text-xl font-bold font-mono text-white">{{ $pedidos->count() }}</span>
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
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Estado Cambiado!</h4>
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
                       placeholder="Buscar por ID de pedido, cliente, mesa o mesero..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
            </div>

            <!-- Filtros de Estado -->
            <div class="flex items-center gap-2 overflow-x-auto shrink-0 pb-1 scrollbar-none">
                <button @click="selectedEstado = ''"
                        :class="selectedEstado === '' ? 'bg-[#121619] text-white' : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Todos
                </button>
                <button @click="selectedEstado = 'En Espera'"
                        :class="selectedEstado === 'En Espera' ? 'bg-orange-500 text-white border-orange-500' : 'bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    En Espera
                </button>
                <button @click="selectedEstado = 'Entregado'"
                        :class="selectedEstado === 'Entregado' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider transition">
                    Entregados
                </button>
            </div>
        </div>

        <!-- Grid de Pedidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="pedido in getFilteredPedidos()" :key="pedido.id">
                <div :class="pedido.estado === 'En Espera' ? 'border-orange-200 bg-orange-50/5' : 'border-gray-200 bg-white'"
                     class="border rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-4">
                    
                    <!-- Encabezado de Pedido Card -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block" x-text="'Pedido #' + pedido.id"></span>
                            <h4 class="text-md font-bold font-serif text-[#2c1d11]" x-text="'Mesa ' + pedido.mesa_numero"></h4>
                            <span class="text-[10px] text-gray-400 font-light" x-text="pedido.fecha"></span>
                        </div>
                        
                        <!-- Badge Estado -->
                        <span :class="pedido.estado === 'En Espera' ? 'bg-orange-100 text-orange-800 border-orange-200' : 'bg-green-100 text-green-800 border-green-200'"
                              class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full border shadow-sm"
                              x-text="pedido.estado">
                        </span>
                    </div>

                    <!-- Datos del pedido (Quién lo tomó / Cliente) -->
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between text-[10px] text-gray-400 uppercase tracking-wider font-semibold">
                            <span>Atendido por</span>
                            <span>Cliente</span>
                        </div>
                        <div class="flex items-center justify-between font-medium text-[#2c1d11]">
                            <span x-text="pedido.mesero_name"></span>
                            <span x-text="pedido.user_name"></span>
                        </div>
                    </div>

                    <!-- Detalles / Productos listados -->
                    <div class="space-y-2">
                        <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold block">Productos ordenados</span>
                        <div class="bg-gray-50/50 rounded-xl p-3 max-h-36 overflow-y-auto divide-y divide-gray-100/60 border border-gray-100">
                            <template x-for="detail in pedido.detalles">
                                <div class="py-1.5 flex items-center justify-between text-xs">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <p class="font-bold text-[#2c1d11] truncate" x-text="detail.producto_nombre"></p>
                                        <span class="text-[10px] text-gray-400" x-text="'Cant: ' + detail.cantidad + ' x $' + detail.precio_unitario"></span>
                                    </div>
                                    <span class="font-mono font-bold text-gray-700 shrink-0" x-text="'$' + detail.subtotal"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Observaciones de la orden -->
                    <div class="bg-yellow-50/30 border border-yellow-100 rounded-xl p-3 space-y-1">
                        <span class="text-[9px] uppercase tracking-wider text-yellow-600 font-bold block">Notas / Comentarios</span>
                        <p class="text-[11px] text-gray-600 font-light italic leading-relaxed" x-text="pedido.observaciones"></p>
                    </div>

                    <!-- Footer y Acciones de Entrega -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-4">
                        <div>
                            <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Total Pedido</span>
                            <span class="text-base font-bold font-mono text-[#2c1d11]" x-text="'$' + pedido.total"></span>
                        </div>
                        
                        <div>
                            <!-- Si el pedido está en espera -->
                            <template x-if="pedido.estado === 'En Espera'">
                                <form :action="'/mesero/pedidos/' + pedido.id + '/status'" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="Entregado">
                                    <button type="submit" class="py-2.5 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-[10px] uppercase tracking-wider transition shadow-sm flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marcar Entregado
                                    </button>
                                </form>
                            </template>
                            
                            <!-- Si ya se entregó -->
                            <template x-if="pedido.estado === 'Entregado'">
                                <span class="text-green-600 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Entregado
                                </span>
                            </template>
                        </div>
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
                <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Intenta modificando tu término de búsqueda o seleccionando otro filtro de estado.</p>
            </div>
        </div>

    </div>
</x-mesero-layout>
