@php
    // Obtener los datos inyectados por el controlador
    // $productos, $movimientos, $categoria, $bodega
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Control de Inventario') }}
    </x-slot>

    <!-- Envoltorio AlpineJS para buscar, paginar y modales de ajuste de stock -->
    <div x-data="{
            searchQuery: '',
            movimientoModalOpen: false,
            movimientoFormAction: '',
            movimientoProductoNombre: '',
            movimientoTipo: 'entrada',
            movimientoCantidad: 1,
            movimientoObservaciones: '',
            currentPage: 1,
            productos: {{ $productos->map(fn($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'descripcion' => $p->descripcion ?? 'Sin descripción',
                'precio' => $p->precio,
                'imagen' => $p->imagen ?? '/uploads/default_product.jpg',
                'stock' => $p->stock
            ])->toJson() }},
            getFilteredProductos() {
                return this.productos.filter(p => {
                    return this.searchQuery === '' || p.nombre.toLowerCase().includes(this.searchQuery.toLowerCase());
                });
            },
            getPaginatedProductos() {
                const start = (this.currentPage - 1) * 6;
                return this.getFilteredProductos().slice(start, start + 6);
            },
            totalPages() {
                return Math.ceil(this.getFilteredProductos().length / 6) || 1;
            }
         }"
         x-init="$watch('searchQuery', () => currentPage = 1)"
         class="space-y-6">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• BODEGA Y BEBIDAS •</span>
                <h3 class="text-2xl font-serif italic text-white">Inventario de Bebidas en Botella</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Supervisa y ajusta las existencias físicas de refrescos, cervezas y bebidas en botella de La Tribu.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Bodega Activa</span>
                <span class="text-xs font-bold text-white bg-white/5 border border-white/10 px-2.5 py-1 rounded-lg uppercase tracking-wider">{{ $bodegaPrincipal->nombre }}</span>
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
                        <h4 class="text-md font-bold text-[#2c1d11]">¡Operación Exitosa!</h4>
                        <p class="text-xs text-gray-500 font-light leading-relaxed">{{ session('success') }}</p>
                    </div>

                    <div class="pt-2">
                        <button @click="successModalOpen = false" class="w-full py-2.5 bg-[#d4af37] hover:bg-[#c29d2e] text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs space-y-1">
                <span class="font-bold block">Por favor corrige los siguientes errores:</span>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- COLUMNA IZQUIERDA Y CENTRAL: LISTADO DE BEBIDAS (Paginación de 6) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between min-h-[520px]">
                <div>
                    <!-- Buscador e Indicador de stock bajo -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                        <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z" />
                            </svg>
                            <span>Stock de Bebidas</span>
                        </h4>

                        <div class="relative w-full sm:max-w-xs">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="searchQuery" 
                                   class="w-full pl-9 pr-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" 
                                   placeholder="Buscar bebida por nombre...">
                        </div>
                    </div>

                    <!-- Tabla de Productos Bebidas -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                    <th class="py-3">Imagen</th>
                                    <th class="py-3">Producto</th>
                                    <th class="py-3">Precio</th>
                                    <th class="py-3 text-center">Stock Físico</th>
                                    <th class="py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-xs">
                                <template x-for="p in getPaginatedProductos()" :key="p.id">
                                    <tr>
                                        <td class="py-3 pr-3">
                                            <img :src="p.imagen" alt="Bebida" class="w-10 h-10 object-cover rounded-xl border border-gray-100 shadow-sm shrink-0">
                                        </td>
                                        <td class="py-3 pr-3">
                                            <h5 class="font-bold text-[#2c1d11]" x-text="p.nombre"></h5>
                                            <p class="text-[10px] text-gray-400 font-light truncate max-w-xs" x-text="p.descripcion"></p>
                                        </td>
                                        <td class="py-3 pr-3 font-bold font-mono text-[#2c1d11]" x-text="'$' + parseFloat(p.precio).toFixed(2)"></td>
                                        <td class="py-3 pr-3 text-center">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono inline-block"
                                                  :class="p.stock <= 5 ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-700 border border-green-100'"
                                                  x-text="p.stock + ' uds'"></span>
                                        </td>
                                        <td class="py-3 text-right whitespace-nowrap">
                                            <!-- Registrar Ajuste / Movimiento -->
                                            <button @click="
                                                movimientoProductoNombre = p.nombre;
                                                movimientoFormAction = '/admin/inventario/movimiento/' + p.id;
                                                movimientoCantidad = 1;
                                                movimientoObservaciones = '';
                                                movimientoModalOpen = true;
                                            " class="px-2.5 py-1 bg-amber-50 text-[#d4af37] border border-amber-100 hover:bg-[#d4af37] hover:text-[#121619] rounded-lg transition mr-1.5 text-[10px] font-bold uppercase tracking-wider">
                                                Ajustar Stock
                                            </button>

                                            <!-- Eliminar -->
                                            <form method="POST" :action="'/admin/inventario/producto/' + p.id" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto y sus movimientos?');" class="inline">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <template x-if="getFilteredProductos().length === 0">
                            <div class="text-center py-12 text-gray-400">
                                <p class="text-xs">No se encontraron bebidas registradas.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Paginación local -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                    <span class="text-[10px] text-gray-400 font-medium">
                        Mostrando página <span x-text="currentPage" class="font-bold text-[#2c1d11]"></span> de <span x-text="totalPages()" class="font-bold text-[#2c1d11]"></span> (<span x-text="getFilteredProductos().length" class="font-bold text-[#2c1d11]"></span> bebidas filtradas)
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

            <!-- COLUMNA DERECHA: FORMULARIO AGREGAR NUEVA BEBIDA -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <span class="text-[9px] tracking-widest text-[#d4af37] font-bold uppercase block mb-1">Nuevo Insumo</span>
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Agregar Bebida</span>
                    </h4>
                </div>

                <form method="POST" action="{{ route('admin.inventario.product.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    <!-- Nombre -->
                    <div class="space-y-1">
                        <label for="nombre" class="font-bold text-[#2c1d11] block">Nombre de la Bebida</label>
                        <input type="text" name="nombre" id="nombre" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" placeholder="Ej. Coca-Cola 350ml">
                    </div>

                    <!-- Precio y Stock inicial -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="precio" class="font-bold text-[#2c1d11] block">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" id="precio" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" placeholder="Ej. 3500.00">
                        </div>
                        <div class="space-y-1">
                            <label for="stock" class="font-bold text-[#2c1d11] block">Stock Inicial</label>
                            <input type="number" name="stock" id="stock" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" placeholder="Ej. 24">
                        </div>
                    </div>

                    <!-- Descripcion -->
                    <div class="space-y-1">
                        <label for="descripcion" class="font-bold text-[#2c1d11] block">Descripción (Opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="2" 
                                  class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition resize-none placeholder-gray-400" placeholder="Marca, volumen, etc."></textarea>
                    </div>

                    <!-- Bodega/Inventario -->
                    <div class="space-y-1">
                        <label for="inventario_id" class="font-bold text-[#2c1d11] block">Inventario / Bodega Destino</label>
                        <select name="inventario_id" id="inventario_id" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            @foreach($bodegas as $b)
                                <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Imagen (Custom Drag/Upload style) -->
                    <div class="space-y-1" x-data="{ fileName: '' }">
                        <label class="font-bold text-[#2c1d11] block mb-1">Imagen de Presentación</label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-[#d4af37] transition bg-[#fdfbf7] cursor-pointer">
                            <input type="file" name="imagen" id="imagen" accept="image/*" required
                                   @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-400 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="block text-[10px] text-gray-500 font-bold" x-text="fileName || 'Subir Foto de Botella'"></span>
                            <span class="block text-[9px] text-gray-400 font-light mt-0.5" x-show="!fileName">JPG, PNG o WEBP (Máx. 2MB)</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#d4af37] hover:bg-[#c29d2e] text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-1.5">
                        Registrar Bebida
                </form>
            </div>
        </div>

        <!-- SECCIÓN INFERIOR: HISTORIAL DE MOVIMIENTOS -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
            <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 border-b border-gray-100 pb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Historial Reciente de Entradas y Salidas</span>
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                            <th class="py-2.5">Bebida</th>
                            <th class="py-2.5">Bodega / Inventario</th>
                            <th class="py-2.5">Tipo</th>
                            <th class="py-2.5 text-center">Cantidad</th>
                            <th class="py-2.5">Usuario responsable</th>
                            <th class="py-2.5">Fecha y hora</th>
                            <th class="py-2.5">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($movimientos as $mov)
                            <tr>
                                <td class="py-3 font-bold text-[#2c1d11]">{{ $mov->producto?->nombre ?? 'Bebida eliminada' }}</td>
                                <td class="py-3 text-gray-500 font-semibold">{{ $mov->inventario?->nombre ?? 'Bodega Principal' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                          :class="{
                                              'bg-green-50 text-green-700': '{{ $mov->tipo_movimiento }}' === 'entrada',
                                              'bg-red-50 text-red-600': '{{ $mov->tipo_movimiento }}' === 'salida'
                                          }">
                                        {{ $mov->tipo_movimiento }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-mono font-bold text-[#2c1d11]">{{ $mov->cantidad }} uds</td>
                                <td class="py-3 text-gray-500">{{ $mov->user?->name ?? 'Usuario inexistente' }}</td>
                                <td class="py-3 text-gray-400 font-mono">{{ $mov->created_at->format('d/m/Y H:i A') }}</td>
                                <td class="py-3 text-gray-500 font-light">{{ $mov->observaciones ?? 'Ajuste regular' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-400">No se registran movimientos recientes en la bitácora.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL FLOTANTE DE AJUSTE DE INVENTARIO (AlpineJS) -->
        <div x-show="movimientoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm border border-gray-200 shadow-2xl space-y-4 text-xs" @click.away="movimientoModalOpen = false"
                 x-show="movimientoModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex flex-col">
                        <span>Ajustar Stock Físico</span>
                        <span class="text-[10px] text-[#d4af37] font-bold uppercase mt-0.5" x-text="movimientoProductoNombre"></span>
                    </h4>
                    <button @click="movimientoModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" :action="movimientoFormAction" class="space-y-4">
                    @csrf

                    <!-- Tipo de movimiento -->
                    <div class="space-y-1">
                        <label class="font-bold text-[#2c1d11] block">Tipo de Ajuste</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="border border-gray-200 p-2.5 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer hover:bg-gray-50 transition"
                                   :class="movimientoTipo === 'entrada' ? 'border-[#d4af37] bg-[#fdfbf7] font-bold text-[#d4af37]' : 'text-gray-400'">
                                <input type="radio" name="tipo_movimiento" value="entrada" x-model="movimientoTipo" class="hidden">
                                <span>⚡ Entrada</span>
                            </label>
                            <label class="border border-gray-200 p-2.5 rounded-xl flex items-center justify-center gap-1.5 cursor-pointer hover:bg-gray-50 transition"
                                   :class="movimientoTipo === 'salida' ? 'border-red-500 bg-red-50/20 font-bold text-red-600' : 'text-gray-400'">
                                <input type="radio" name="tipo_movimiento" value="salida" x-model="movimientoTipo" class="hidden">
                                <span>⚠️ Salida</span>
                            </label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="space-y-1">
                        <label for="cantidad" class="font-bold text-[#2c1d11] block">Cantidad a Ajustar</label>
                        <input type="number" name="cantidad" id="cantidad" min="1" required x-model="movimientoCantidad"
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Bodega/Inventario -->
                    <div class="space-y-1">
                        <label for="modal_inventario_id" class="font-bold text-[#2c1d11] block">Inventario / Bodega de Destino</label>
                        <select name="inventario_id" id="modal_inventario_id" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            @foreach($bodegas as $b)
                                <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Observaciones -->
                    <div class="space-y-1">
                        <label for="observaciones" class="font-bold text-[#2c1d11] block">Motivo / Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="2" x-model="movimientoObservaciones"
                                  class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition resize-none" placeholder="Compra, merma, rotura, etc."></textarea>
                    </div>

                    <!-- Acciones -->
                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" @click="movimientoModalOpen = false" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition">
                            Cancelar
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-[#c29d2e] text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Procesar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
