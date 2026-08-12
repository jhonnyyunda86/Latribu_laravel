@php
    // Obtener las categorías y productos reales de la base de datos
    $categorias = \App\Models\Categoria::all();
    $productos = \App\Models\Producto::with('categoria')->get();
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Gestión del Menú') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para gestionar el estado del Modal de Edición e Instant Search -->
    <div x-data="{ editModalOpen: false, editFormAction: '', editNombre: '', editCategoriaId: '', editPrecio: '', editDescripcion: '', editImagen: '', editDisponible: true, searchQuery: '', selectedCategory: '' }" class="space-y-6">
        
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• PLATILLOS Y BEBIDAS •</span>
                <h3 class="text-2xl font-serif italic text-white">Carta del Restaurante</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Registra y administra las opciones del menú para clientes y meseros.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Total Productos</span>
                <span class="text-2xl font-bold font-mono text-white">{{ $productos->count() }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LISTADO DE PRODUCTOS (Columna izquierda) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    
                    <!-- Encabezado y Filtros en la misma sección -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                        <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>Productos en Menú</span>
                        </h4>

                        <!-- Sección de Filtros Activa -->
                        <div class="flex flex-col sm:flex-row items-stretch gap-3 w-full md:max-w-md">
                            <!-- Búsqueda por Nombre -->
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" x-model="searchQuery" 
                                       class="w-full pl-9 pr-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition placeholder-gray-400" 
                                       placeholder="Buscar platillo...">
                            </div>

                            <!-- Filtrar por Categoría -->
                            <select x-model="selectedCategory" 
                                    class="px-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($productos->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="text-xs">No hay productos registrados en el menú actualmente.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                        <th class="py-3">Producto</th>
                                        <th class="py-3">Categoría</th>
                                        <th class="py-3">Precio</th>
                                        <th class="py-3">Estado</th>
                                        <th class="py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-xs">
                                    @foreach($productos as $producto)
                                        <!-- Fila de Tabla con Filtro Dinámico de AlpineJS -->
                                        <tr x-show="(searchQuery === '' || '{{ strtolower(addslashes($producto->nombre)) }}'.includes(searchQuery.toLowerCase())) && (selectedCategory === '' || '{{ $producto->categoria_id }}' === selectedCategory)">
                                            <td class="py-3.5 pr-3">
                                                <div class="flex items-center gap-3">
                                                    @if($producto->imagen)
                                                        <img src="{{ $producto->imagen }}" class="w-10 h-10 object-cover rounded-lg border border-gray-100 shrink-0" alt="{{ $producto->nombre }}">
                                                    @else
                                                        <div class="w-10 h-10 bg-[#FAF4EB] text-[#d4af37] rounded-lg flex items-center justify-center font-bold text-xs shrink-0 border border-gray-100">
                                                            {{ strtoupper(substr($producto->nombre, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h5 class="font-bold text-[#2c1d11]">{{ $producto->nombre }}</h5>
                                                        <p class="text-[10px] text-gray-400 font-light truncate max-w-xs">{{ $producto->descripcion }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 text-gray-500 font-medium">
                                                {{ $producto->categoria?->nombre ?? 'Sin Categoría' }}
                                            </td>
                                            <td class="py-3.5 font-bold font-mono text-[#2c1d11]">
                                                ${{ number_format($producto->precio, 2) }}
                                            </td>
                                            <td class="py-3.5">
                                                @if($producto->disponible)
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 uppercase tracking-wider">Disponible</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-600 uppercase tracking-wider">Agotado</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 text-right whitespace-nowrap">
                                                <!-- Botón Editar con Carga Dinámica en AlpineJS -->
                                                <button @click="
                                                    editNombre = '{{ addslashes($producto->nombre) }}';
                                                    editCategoriaId = '{{ $producto->categoria_id }}';
                                                    editPrecio = '{{ $producto->precio }}';
                                                    editDescripcion = '{{ addslashes($producto->descripcion) }}';
                                                    editImagen = '{{ $producto->imagen }}';
                                                    editDisponible = {{ $producto->disponible ? 'true' : 'false' }};
                                                    editFormAction = '{{ route('admin.menu.update', $producto->id) }}';
                                                    editModalOpen = true;
                                                " class="inline-flex items-center justify-center p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition mr-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Formulario para Eliminar con Confirmación -->
                                                <form method="POST" action="{{ route('admin.menu.destroy', $producto->id) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- FORMULARIO DE AGREGAR PRODUCTO (Columna derecha) -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h4 class="text-md font-bold text-[#2c1d11] mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Agregar Producto</span>
                    </h4>

                    <form method="POST" action="{{ route('admin.menu.store') }}" class="space-y-4 text-xs">
                        @csrf

                        <!-- Nombre -->
                        <div class="space-y-1">
                            <label for="nombre" class="font-bold text-[#2c1d11] block">Nombre del Producto</label>
                            <input type="text" name="nombre" id="nombre" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="Ej. Pizza Margarita">
                        </div>

                        <!-- Categoria Dropdown -->
                        <div class="space-y-1">
                            <label for="categoria_id" class="font-bold text-[#2c1d11] block">Categoría</label>
                            <select name="categoria_id" id="categoria_id" required 
                                    class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                                <option value="" disabled selected>Selecciona una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Precio -->
                        <div class="space-y-1">
                            <label for="precio" class="font-bold text-[#2c1d11] block">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" id="precio" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="Ej. 12.50">
                        </div>

                        <!-- Descripción -->
                        <div class="space-y-1">
                            <label for="descripcion" class="font-bold text-[#2c1d11] block">Descripción</label>
                            <textarea name="descripcion" id="descripcion" rows="3" 
                                      class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition resize-none" 
                                      placeholder="Detalla los ingredientes o tamaño..."></textarea>
                        </div>

                        <!-- Imagen URL -->
                        <div class="space-y-1">
                            <label for="imagen" class="font-bold text-[#2c1d11] block">URL de la Imagen (Opcional)</label>
                            <input type="url" name="imagen" id="imagen" 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="https://ejemplo.com/imagen.jpg">
                        </div>

                        <!-- Disponible Checkbox -->
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="disponible" id="disponible" value="1" checked 
                                   class="rounded text-[#d4af37] focus:ring-[#d4af37] border-gray-300">
                            <label for="disponible" class="font-bold text-[#2c1d11]">Disponible de inmediato</label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                                Registrar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Producto (AlpineJS) -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-gray-200 shadow-xl space-y-4 text-xs" @click.away="editModalOpen = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Editar Producto</span>
                    </h4>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" :action="editFormAction" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="space-y-1">
                        <label for="edit_nombre" class="font-bold text-[#2c1d11] block">Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit_nombre" x-model="editNombre" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Categoria Dropdown -->
                    <div class="space-y-1">
                        <label for="edit_categoria_id" class="font-bold text-[#2c1d11] block">Categoría</label>
                        <select name="categoria_id" id="edit_categoria_id" x-model="editCategoriaId" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="space-y-1">
                        <label for="edit_precio" class="font-bold text-[#2c1d11] block">Precio ($)</label>
                        <input type="number" step="0.01" name="precio" id="edit_precio" x-model="editPrecio" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-1">
                        <label for="edit_descripcion" class="font-bold text-[#2c1d11] block">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" rows="3" x-model="editDescripcion"
                                  class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition resize-none"></textarea>
                    </div>

                    <!-- Imagen URL -->
                    <div class="space-y-1">
                        <label for="edit_imagen" class="font-bold text-[#2c1d11] block">URL de la Imagen (Opcional)</label>
                        <input type="url" name="imagen" id="edit_imagen" x-model="editImagen"
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Disponible Checkbox -->
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="disponible" id="edit_disponible" value="1" :checked="editDisponible" 
                               class="rounded text-[#d4af37] focus:ring-[#d4af37] border-gray-300">
                        <label for="edit_disponible" class="font-bold text-[#2c1d11]">Disponible</label>
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" @click="editModalOpen = false" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition">
                            Cancelar
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
