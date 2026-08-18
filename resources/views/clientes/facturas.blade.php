@php
    // La lista de facturas es provista por el controlador
@endphp

<x-cliente-layout>
    <x-slot name="header">
        {{ __('Mis Facturas y Comprobantes') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para búsqueda y visualización de detalles -->
    <div x-data="{ 
            searchQuery: '', 
            selectedFactura: null,
            facturas: {{ $facturas->map(fn($f) => [
                'id' => $f->id,
                'numero' => $f->numero_factura,
                'mesa_numero' => $f->pedido?->mesa?->numero ?? 'Consumo Online',
                'subtotal' => number_format($f->subtotal, 0, ',', '.'),
                'impuesto' => number_format($f->impuesto, 0, ',', '.'),
                'total' => number_format($f->total, 0, ',', '.'),
                'metodo_pago' => ucfirst($f->metodo_pago),
                'estado_pago' => ucfirst($f->estado_pago),
                'fecha' => $f->created_at->format('d/m/Y H:i'),
                'detalles' => $f->detalles->map(fn($d) => [
                    'producto' => $d->nombre_producto,
                    'cantidad' => $d->cantidad,
                    'precio' => number_format($d->precio_unitario, 0, ',', '.'),
                    'subtotal' => number_format($d->subtotal, 0, ',', '.')
                ])
            ])->toJson() }},
            getFilteredFacturas() {
                return this.facturas.filter(f => {
                    return this.searchQuery === '' || 
                        f.numero.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        f.fecha.includes(this.searchQuery) ||
                        f.mesa_numero.toString().includes(this.searchQuery);
                });
            },
            showDetails(factura) {
                this.selectedFactura = factura;
            }
         }"
         class="space-y-6">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• COMPROBANTES •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Mis Facturas</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Consulta el historial de facturas generadas por tus consumos, reservas y pedidos en La Tribu.</p>
                </div>
                <div class="text-right bg-white/5 border border-white/10 p-3 rounded-xl min-w-[120px] text-center">
                    <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold mb-0.5">Facturas Totales</span>
                    <span class="text-xl font-bold font-mono text-white">{{ $facturas->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="Buscar factura por número, fecha o mesa..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
            </div>
        </div>

        <!-- Grid de Facturas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="factura in getFilteredFacturas()" :key="factura.id">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-4">
                    
                    <!-- Encabezado Tarjeta -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block" x-text="factura.numero"></span>
                            <h4 class="text-md font-bold font-serif text-[#2c1d11]" x-text="'Mesa ' + factura.mesa_numero"></h4>
                            <span class="text-[10px] text-gray-400 font-light" x-text="factura.fecha"></span>
                        </div>
                        
                        <!-- Badge Estado de Pago -->
                        <span :class="{
                                'bg-green-100 text-green-800 border-green-200': factura.estado_pago === 'Pagado',
                                'bg-orange-100 text-orange-800 border-orange-200': factura.estado_pago === 'Pendiente',
                                'bg-red-100 text-red-800 border-red-200': factura.estado_pago === 'Anulado'
                              }"
                              class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full border shadow-sm"
                              x-text="factura.estado_pago">
                        </span>
                    </div>

                    <!-- Datos Generales -->
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Método de Pago:</span>
                            <span class="font-bold text-[#2c1d11]" x-text="factura.metodo_pago"></span>
                        </div>
                        <div class="flex justify-between text-[#2c1d11] font-bold text-sm bg-gray-50 p-2.5 rounded-xl border border-gray-100 mt-2">
                            <span>Total Facturado:</span>
                            <span class="font-mono" x-text="'$' + factura.total"></span>
                        </div>
                    </div>

                    <!-- Botones de Acción (Detalles y POS) -->
                    <div class="pt-2 flex items-center gap-2">
                        <button @click="showDetails(factura)" class="w-1/2 py-2 bg-[#121619] hover:bg-black text-white hover:text-[#d4af37] font-bold rounded-xl text-[10px] uppercase tracking-wider transition border border-[#d4af37]/20 flex items-center justify-center gap-1 shadow-sm">
                            Detalles
                        </button>
                        <a :href="'/cliente/facturas/' + factura.id + '/pdf'" class="w-1/2 py-2 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-[10px] uppercase tracking-wider transition text-center shadow-sm flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Ticket POS
                        </a>
                    </div>

                </div>
            </template>
        </div>

        <!-- Estado Vacío -->
        <div x-show="getFilteredFacturas().length === 0" class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm space-y-3">
            <div class="w-16 h-16 bg-[#FAF4EB] text-[#d4af37] rounded-full flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="text-md font-bold text-[#2c1d11]">No tienes facturas disponibles</h4>
                <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Tus comprobantes se registrarán aquí cuando realices pedidos en nuestro restaurante.</p>
            </div>
        </div>

        <!-- MODAL DETALLE DE FACTURA -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-show="selectedFactura !== null"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <div class="bg-white rounded-2xl w-full max-w-md p-6 space-y-5 shadow-2xl relative"
                 @click.away="selectedFactura = null"
                 x-show="selectedFactura !== null"
                 x-transition:enter="transition ease-out duration-300 transform scale-95"
                 x-transition:enter-start="scale-95"
                 x-transition:enter-end="scale-100"
                 x-transition:leave="transition ease-in duration-200 transform scale-100"
                 x-transition:leave-start="scale-100"
                 x-transition:leave-end="scale-95">
                
                <!-- Botón Cerrar -->
                <button @click="selectedFactura = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Encabezado Modal -->
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-[#d4af37] font-bold block mb-1" x-text="selectedFactura?.numero"></span>
                    <h4 class="text-lg font-serif italic font-bold text-[#2c1d11]">Detalles del Consumo</h4>
                    <p class="text-[11px] text-gray-500 font-light" x-text="'Mesa ' + selectedFactura?.mesa_numero + ' • ' + selectedFactura?.fecha"></p>
                </div>

                <!-- Detalles de Productos -->
                <div class="space-y-4">
                    <div class="max-h-48 overflow-y-auto divide-y divide-gray-100 pr-1">
                        <template x-for="item in selectedFactura?.detalles">
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
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs space-y-1.5">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-mono" x-text="'$' + selectedFactura?.subtotal"></span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Impuesto (IVA 0%)</span>
                            <span class="font-mono" x-text="'$' + selectedFactura?.impuesto"></span>
                        </div>
                        <div class="border-t border-gray-200 my-1 pt-1.5 flex justify-between font-bold text-[#2c1d11]">
                            <span>Total</span>
                            <span class="font-mono" x-text="'$' + selectedFactura?.total"></span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción en Modal (Cerrar y Descargar) -->
                <div class="pt-2 flex items-center gap-3">
                    <button @click="selectedFactura = null" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider transition">
                        Cerrar
                    </button>
                    <a :href="'/cliente/facturas/' + selectedFactura?.id + '/pdf'" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider text-center transition shadow-sm flex items-center justify-center gap-1.5 font-sans">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Descargar POS
                    </a>
                </div>

            </div>
        </div>

    </div>
</x-cliente-layout>
