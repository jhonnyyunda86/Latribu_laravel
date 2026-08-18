@php
    // Las categorías, productos y mesas son provistos por el controlador
@endphp

<x-mesero-layout>
    <x-slot name="header">
        {{ __('Menú Digital de Consulta y Comandas') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para gestionar búsqueda, filtros y comanda activa -->
    <div x-data="{ 
            searchQuery: '', 
            selectedCategory: '',
            cart: [],
            selectedMesaId: '{{ request()->query('mesa_id', '') }}',
            observaciones: '',
            mobileCartOpen: false,
            products: {{ $productos->map(fn($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'descripcion' => $p->descripcion ?? 'Sin descripción disponible.',
                'precio' => $p->precio,
                'precio_formateado' => number_format($p->precio, 0, ',', '.'),
                'imagen' => $p->imagen ?? '',
                'disponible' => (bool)$p->disponible,
                'stock' => $p->stock ?? 0,
                'categoria_id' => $p->categoria_id,
                'categoria_nombre' => $p->categoria?->nombre ?? 'Sin Categoría'
            ])->toJson() }},
            getFilteredProducts() {
                return this.products.filter(p => {
                    const matchesSearch = this.searchQuery === '' || 
                        p.nombre.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        p.descripcion.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesCategory = this.selectedCategory === '' || p.categoria_id.toString() === this.selectedCategory.toString();
                    return matchesSearch && matchesCategory;
                });
            },
            addToCart(product) {
                if (!product.disponible) return;
                const existing = this.cart.find(item => item.id === product.id);
                if (existing) {
                    if (existing.qty < product.stock || product.stock === 0) {
                        existing.qty++;
                    }
                } else {
                    this.cart.push({
                        id: product.id,
                        nombre: product.nombre,
                        precio: parseFloat(product.precio),
                        precio_formateado: product.precio_formateado,
                        qty: 1,
                        stock: product.stock
                    });
                }
            },
            removeFromCart(productId) {
                this.cart = this.cart.filter(item => item.id !== productId);
            },
            updateQty(productId, delta) {
                const item = this.cart.find(i => i.id === productId);
                if (!item) return;
                const newQty = item.qty + delta;
                if (newQty <= 0) {
                    this.removeFromCart(productId);
                } else {
                    // Si el stock es limitado, validar el límite
                    if (item.stock > 0 && newQty > item.stock) {
                        return;
                    }
                    item.qty = newQty;
                }
            },
            getCartTotal() {
                const total = this.cart.reduce((sum, item) => sum + (item.precio * item.qty), 0);
                return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(total);
            },
            getCartItemCount() {
                return this.cart.reduce((sum, item) => sum + item.qty, 0);
            }
         }"
         class="space-y-6 relative pb-20 lg:pb-0">

        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-[#d4af37]/20">
            <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-64 h-64 bg-[#d4af37]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• CARTA VIGENTE Y COMANDAS •</span>
                    <h3 class="text-2xl font-serif tracking-wide italic mb-1">Toma de Pedidos</h3>
                    <p class="text-xs text-gray-400 max-w-xl font-light">Busca los productos solicitados, asígnalos a una mesa y envía el pedido directamente a cocina.</p>
                </div>
                <div class="text-right bg-white/5 border border-white/10 p-3 rounded-xl min-w-[120px] text-center hidden sm:block">
                    <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold mb-0.5">Productos</span>
                    <span class="text-xl font-bold font-mono text-white">{{ $productos->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Layout de dos columnas: Menú y Comanda -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- COLUMNA DEL MENÚ DE PRODUCTOS (2/3 de ancho) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Barra de Búsqueda y Filtros de Categorías -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 space-y-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input x-model="searchQuery" 
                               type="text" 
                               placeholder="Buscar por nombre, descripción o ingrediente..." 
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#d4af37]/40 focus:border-[#d4af37] transition-all">
                    </div>

                    <!-- Categorías Selector Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        <button @click="selectedCategory = ''"
                                :class="selectedCategory === '' ? 'bg-[#121619] text-white border-[#121619]' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-gray-200'"
                                class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider border transition-all duration-200 shrink-0">
                            Todos
                        </button>
                        @foreach($categorias as $cat)
                            <button @click="selectedCategory = '{{ $cat->id }}'"
                                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#121619] text-white border-[#121619]' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-gray-200'"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider border transition-all duration-200 shrink-0">
                                {{ $cat->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Grid de Productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <template x-for="product in getFilteredProducts()" :key="product.id">
                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm flex flex-col justify-between hover:shadow-md transition duration-300 relative group">
                            
                            <!-- Estatus Disponibilidad Flotante -->
                            <div class="absolute top-3 right-3 z-10">
                                <template x-if="product.disponible">
                                    <span class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full bg-green-100 text-green-800 shadow-sm">
                                        Disponible
                                    </span>
                                </template>
                                <template x-if="!product.disponible">
                                    <span class="text-[9px] uppercase tracking-wider font-extrabold px-2.5 py-1 rounded-full bg-red-100 text-red-800 shadow-sm">
                                        Agotado
                                    </span>
                                </template>
                            </div>

                            <div>
                                <!-- Contenedor Imagen -->
                                <div class="h-44 bg-gray-100 relative overflow-hidden flex items-center justify-center border-b border-gray-100">
                                    <template x-if="product.imagen">
                                        <img :src="product.imagen" :alt="product.nombre" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    </template>
                                    <template x-if="!product.imagen">
                                        <div class="flex flex-col items-center justify-center text-gray-400 p-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#d4af37]/40 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <span class="text-[10px] uppercase font-bold tracking-widest text-[#2c1d11]/40">La Tribu</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Detalles -->
                                <div class="p-5 space-y-2">
                                    <span class="text-[9px] uppercase tracking-widest text-[#d4af37] font-bold block" x-text="product.categoria_nombre"></span>
                                    <h4 class="text-base font-bold text-[#2c1d11] tracking-tight line-clamp-1" x-text="product.nombre"></h4>
                                    <p class="text-xs text-gray-500 font-light leading-relaxed line-clamp-2" x-text="product.descripcion"></p>
                                </div>
                            </div>

                            <!-- Footer del Producto / Acción -->
                            <div class="px-5 pb-5 pt-3 border-t border-gray-100 flex flex-col gap-3 bg-gray-50/50 rounded-b-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Precio</span>
                                        <span class="text-sm font-bold font-mono text-[#2c1d11]">$<span x-text="product.precio_formateado"></span></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[9px] text-gray-400 block uppercase tracking-wider font-semibold">Stock</span>
                                        <span class="text-xs font-bold font-mono" :class="product.stock > 0 ? 'text-[#2c1d11]' : 'text-red-500'" x-text="product.stock + ' uds'"></span>
                                    </div>
                                </div>
                                <button @click="addToCart(product)"
                                        :disabled="!product.disponible"
                                        :class="product.disponible ? 'bg-[#121619] hover:bg-black text-white hover:text-[#d4af37]' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                        class="w-full py-2.5 rounded-xl text-xs uppercase tracking-wider font-bold transition flex items-center justify-center gap-2 border border-transparent hover:border-[#d4af37]/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Añadir a la Comanda
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Estado Vacío -->
                <div x-show="getFilteredProducts().length === 0" class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm space-y-3">
                    <div class="w-16 h-16 bg-[#FAF4EB] text-[#d4af37] rounded-full flex items-center justify-center mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-md font-bold text-[#2c1d11]">No se encontraron productos</h4>
                        <p class="text-xs text-gray-500 font-light max-w-md mx-auto">Intenta modificando tu término de búsqueda o seleccionando otra categoría en los filtros.</p>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DE LA COMANDA ACTIVA (1/3 de ancho, pegajosa en desktop) -->
            <div class="hidden lg:block lg:sticky lg:top-20">
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h4 class="text-md font-serif italic text-[#2c1d11] font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Comanda de Mesa
                        </h4>
                        <span class="text-xs font-mono font-bold bg-[#FAF4EB] text-[#d4af37] px-2.5 py-1 rounded-full" x-text="getCartItemCount() + ' ítems'"></span>
                    </div>

                    <!-- Si el carrito está vacío -->
                    <template x-if="cart.length === 0">
                        <div class="py-8 text-center text-gray-400 space-y-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <p class="text-xs font-light">No has seleccionado productos aún. Añade productos desde la carta.</p>
                        </div>
                    </template>

                    <!-- Si hay productos en la comanda -->
                    <template x-if="cart.length > 0">
                        <div class="space-y-4">
                            <!-- Lista de items en comanda -->
                            <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 pr-1">
                                <template x-for="item in cart" :key="item.id">
                                    <div class="py-3 flex items-center justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-[#2c1d11] truncate" x-text="item.nombre"></h5>
                                            <span class="text-[10px] text-gray-400 font-mono">$<span x-text="item.precio_formateado"></span> c/u</span>
                                        </div>
                                        
                                        <!-- Control Cantidad -->
                                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1.5 shrink-0">
                                            <button @click="updateQty(item.id, -1)" class="w-5 h-5 bg-white text-[#2c1d11] rounded shadow-sm hover:bg-gray-200 transition font-bold flex items-center justify-center text-xs">-</button>
                                            <span class="text-xs font-mono font-bold w-6 text-center" x-text="item.qty"></span>
                                            <button @click="updateQty(item.id, 1)" class="w-5 h-5 bg-white text-[#2c1d11] rounded shadow-sm hover:bg-gray-200 transition font-bold flex items-center justify-center text-xs">+</button>
                                        </div>

                                        <!-- Quitar Item -->
                                        <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700 p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Total -->
                            <div class="border-t border-gray-100 pt-3 flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                                <span class="text-xs uppercase tracking-wider font-bold text-gray-500">Total Estimado</span>
                                <span class="text-md font-bold font-mono text-[#2c1d11]" x-text="getCartTotal()"></span>
                            </div>

                            <!-- Formulario de Registro de Comanda -->
                            <form action="{{ route('mesero.pedidos.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <!-- Selección de Mesa -->
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 block uppercase tracking-widest mb-1.5">Asignar Mesa *</label>
                                    <select name="mesa_id" x-model="selectedMesaId" required class="w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:ring-[#d4af37] focus:border-[#d4af37]">
                                        <option value="">Seleccionar mesa...</option>
                                        @foreach($mesas as $mesa)
                                            <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} (Cap: {{ $mesa->capacidad }}) - {{ $mesa->estado }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Inputs ocultos dinámicos del carrito -->
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <div>
                                        <input type="hidden" :name="'productos[' + index + '][id]'" :value="item.id">
                                        <input type="hidden" :name="'productos[' + index + '][cantidad]'" :value="item.qty">
                                    </div>
                                </template>

                                <!-- Observaciones del pedido -->
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 block uppercase tracking-widest mb-1.5">Instrucciones de Cocina</label>
                                    <textarea name="observaciones" x-model="observaciones" placeholder="Ej: Té frío sin azúcar, hamburguesa sin cebolla, etc..." class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs h-20 focus:ring-[#d4af37] focus:border-[#d4af37] font-light leading-relaxed"></textarea>
                                </div>

                                <button type="submit" 
                                        :disabled="cart.length === 0 || !selectedMesaId"
                                        class="w-full py-3 bg-[#d4af37] disabled:bg-gray-200 disabled:text-gray-400 hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Enviar Comanda a Cocina
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- BOTÓN FLOTANTE COMANDA MÓVIL (Visible solo en pantallas pequeñas si hay ítems) -->
        <div class="fixed bottom-6 right-6 lg:hidden z-40" x-show="cart.length > 0" x-transition>
            <button @click="mobileCartOpen = true" class="bg-[#d4af37] text-[#121619] p-4 rounded-full shadow-2xl flex items-center gap-2 font-bold animate-pulse hover:scale-105 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-xs uppercase tracking-wider font-extrabold" x-text="'Comanda (' + getCartItemCount() + ')'"></span>
            </button>
        </div>

        <!-- DETALLE DE COMANDA MÓVIL (MODAL BOTTOM-SHEET) -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-end justify-center lg:hidden"
             x-show="mobileCartOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <div class="bg-white w-full rounded-t-3xl max-h-[85vh] p-6 overflow-y-auto space-y-4 shadow-2xl"
                 @click.away="mobileCartOpen = false"
                 x-show="mobileCartOpen"
                 x-transition:enter="transition ease-out duration-300 transform translate-y-full"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform translate-y-0"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h4 class="text-md font-serif italic text-[#2c1d11] font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Nueva Comanda
                    </h4>
                    <button @click="mobileCartOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Lista de items (Móvil) -->
                <div class="divide-y divide-gray-100 max-h-[30vh] overflow-y-auto">
                    <template x-for="item in cart" :key="item.id">
                        <div class="py-3 flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-bold text-[#2c1d11] truncate" x-text="item.nombre"></h5>
                                <span class="text-[10px] text-gray-400 font-mono">$<span x-text="item.precio_formateado"></span> c/u</span>
                            </div>
                            
                            <!-- Control Cantidad -->
                            <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1.5 shrink-0">
                                <button @click="updateQty(item.id, -1)" class="w-6 h-6 bg-white text-[#2c1d11] rounded shadow-sm hover:bg-gray-200 transition font-bold flex items-center justify-center text-xs">-</button>
                                <span class="text-xs font-mono font-bold w-6 text-center" x-text="item.qty"></span>
                                <button @click="updateQty(item.id, 1)" class="w-6 h-6 bg-white text-[#2c1d11] rounded shadow-sm hover:bg-gray-200 transition font-bold flex items-center justify-center text-xs">+</button>
                            </div>

                            <!-- Quitar Item -->
                            <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Total (Móvil) -->
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="text-xs uppercase tracking-wider font-bold text-gray-500">Total Estimado</span>
                    <span class="text-md font-bold font-mono text-[#2c1d11]" x-text="getCartTotal()"></span>
                </div>

                <!-- Formulario de comanda (Móvil) -->
                <form action="{{ route('mesero.pedidos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Selección de Mesa -->
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 block uppercase tracking-widest mb-1.5">Asignar Mesa *</label>
                        <select name="mesa_id" x-model="selectedMesaId" required class="w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:ring-[#d4af37] focus:border-[#d4af37]">
                            <option value="">Seleccionar mesa...</option>
                            @foreach($mesas as $mesa)
                                <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} (Cap: {{ $mesa->capacidad }}) - {{ $mesa->estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Inputs ocultos -->
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div>
                            <input type="hidden" :name="'productos[' + index + '][id]'" :value="item.id">
                            <input type="hidden" :name="'productos[' + index + '][cantidad]'" :value="item.qty">
                        </div>
                    </template>

                    <!-- Observaciones -->
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 block uppercase tracking-widest mb-1.5">Instrucciones de Cocina</label>
                        <textarea name="observaciones" x-model="observaciones" placeholder="Ej: Té frío sin azúcar, hamburguesa sin cebolla, etc..." class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs h-20 focus:ring-[#d4af37] focus:border-[#d4af37] font-light leading-relaxed"></textarea>
                    </div>

                    <button type="submit" 
                            :disabled="cart.length === 0 || !selectedMesaId"
                            class="w-full py-3 bg-[#d4af37] disabled:bg-gray-200 disabled:text-gray-400 hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Enviar Comanda a Cocina
                    </button>
                </form>

            </div>
        </div>

    </div>
</x-mesero-layout>
