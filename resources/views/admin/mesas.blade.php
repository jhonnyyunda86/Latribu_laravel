@php
    // Obtener todas las mesas reales de la base de datos
    $mesas = \App\Models\Mesa::all();
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Gestión de Mesas') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para CRUD, Búsqueda e Instant Pagination de Mesas -->
    <div x-data="{ 
            editModalOpen: false, 
            editFormAction: '', 
            editNumero: '', 
            editCapacidad: '', 
            editEstado: '', 
            editUbicacion: '', 
            editActivo: true, 
            searchQuery: '', 
            selectedEstado: '',
            currentPage: 1,
            mesas: {{ $mesas->map(fn($m) => [
                'id' => $m->id,
                'numero' => $m->numero,
                'capacidad' => $m->capacidad,
                'estado' => $m->estado,
                'ubicacion' => $m->ubicacion ?? 'Sin especificar',
                'activo' => $m->activo
            ])->toJson() }},
            getFilteredMesas() {
                return this.mesas.filter(m => {
                    const matchesSearch = this.searchQuery === '' || m.numero.toLowerCase().includes(this.searchQuery.toLowerCase()) || m.ubicacion.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesEstado = this.selectedEstado === '' || m.estado === this.selectedEstado;
                    return matchesSearch && matchesEstado;
                });
            },
            getPaginatedMesas() {
                const start = (this.currentPage - 1) * 6;
                return this.getFilteredMesas().slice(start, start + 6);
            },
            totalPages() {
                return Math.ceil(this.getFilteredMesas().length / 6) || 1;
            }
         }"
         x-init="$watch('searchQuery', () => currentPage = 1); $watch('selectedEstado', () => currentPage = 1);"
         class="space-y-6">
        
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• SALÓN Y DISTRIBUCIÓN •</span>
                <h3 class="text-2xl font-serif italic text-white">Mapa de Mesas</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Registra y monitorea el estado y la ubicación de las mesas físicas en el restaurante.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Total Mesas</span>
                <span class="text-2xl font-bold font-mono text-white">{{ $mesas->count() }}</span>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- LISTADO DE MESAS -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between min-h-[580px]">
                    <div>
                        <!-- Filtros y Cabecera -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                            <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Mesas Disponibles</span>
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
                                           placeholder="Buscar por número o zona...">
                                </div>

                                <select x-model="selectedEstado" 
                                        class="px-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                                    <option value="">Todos los estados</option>
                                    <option value="Disponible">Disponible</option>
                                    <option value="Ocupada">Ocupada</option>
                                    <option value="Reservada">Reservada</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabla de Mesas -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                        <th class="py-3">Número de Mesa</th>
                                        <th class="py-3">Capacidad</th>
                                        <th class="py-3">Ubicación / Zona</th>
                                        <th class="py-3">Estado</th>
                                        <th class="py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-xs">
                                    <template x-for="mesa in getPaginatedMesas()" :key="mesa.id">
                                        <tr>
                                            <td class="py-3.5 pr-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 border border-gray-100"
                                                         :class="{
                                                             'bg-green-50 text-green-700': mesa.estado === 'Disponible',
                                                             'bg-red-50 text-red-600': mesa.estado === 'Ocupada',
                                                             'bg-amber-50 text-[#d4af37]': mesa.estado === 'Reservada',
                                                             'bg-gray-100 text-gray-500': mesa.estado === 'Mantenimiento'
                                                         }">
                                                         <span x-text="mesa.numero"></span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-[#2c1d11]" x-text="'Mesa ' + mesa.numero"></h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 text-gray-500 font-semibold" x-text="mesa.capacidad + ' personas'"></td>
                                            <td class="py-3.5 text-gray-500 font-medium" x-text="mesa.ubicacion"></td>
                                            <td class="py-3.5">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                                      :class="{
                                                          'bg-green-50 text-green-700': mesa.estado === 'Disponible',
                                                          'bg-red-50 text-red-600': mesa.estado === 'Ocupada',
                                                          'bg-[#FAF4EB] text-[#d4af37]': mesa.estado === 'Reservada',
                                                          'bg-gray-100 text-gray-500': mesa.estado === 'Mantenimiento'
                                                      }"
                                                      x-text="mesa.estado"></span>
                                            </td>
                                            <td class="py-3.5 text-right whitespace-nowrap">
                                                <!-- Botón Editar -->
                                                <button @click="
                                                    editNumero = mesa.numero;
                                                    editCapacidad = mesa.capacidad;
                                                    editEstado = mesa.estado;
                                                    editUbicacion = mesa.ubicacion;
                                                    editActivo = mesa.activo;
                                                    editFormAction = '/admin/mesas/' + mesa.id;
                                                    editModalOpen = true;
                                                " class="inline-flex items-center justify-center p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition mr-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Eliminar -->
                                                <form method="POST" :action="'/admin/mesas/' + mesa.id" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta mesa?');" class="inline">
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
                            
                            <template x-if="getFilteredMesas().length === 0">
                                <div class="text-center py-12 text-gray-400">
                                    <p class="text-xs">No se encontraron mesas registradas.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Controles de Paginación Fijos abajo -->
                    <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                        <span class="text-[10px] text-gray-400 font-medium">
                            Mostrando página <span x-text="currentPage" class="font-bold text-[#2c1d11]"></span> de <span x-text="totalPages()" class="font-bold text-[#2c1d11]"></span> (<span x-text="getFilteredMesas().length" class="font-bold text-[#2c1d11]"></span> mesas filtradas)
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

            <!-- FORMULARIO DE AGREGAR MESA -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h4 class="text-md font-bold text-[#2c1d11] mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Agregar Mesa</span>
                    </h4>

                    <form method="POST" action="{{ route('admin.mesas.store') }}" class="space-y-4 text-xs">
                        @csrf

                        <!-- Número -->
                        <div class="space-y-1">
                            <label for="numero" class="font-bold text-[#2c1d11] block">Número o Código</label>
                            <input type="text" name="numero" id="numero" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="Ej. 1, 2B, VIP-1">
                        </div>

                        <!-- Capacidad -->
                        <div class="space-y-1">
                            <label for="capacidad" class="font-bold text-[#2c1d11] block">Capacidad (Personas)</label>
                            <input type="number" name="capacidad" id="capacidad" min="1" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="Ej. 4">
                        </div>

                        <!-- Estado -->
                        <div class="space-y-1">
                            <label for="estado" class="font-bold text-[#2c1d11] block">Estado Inicial</label>
                            <select name="estado" id="estado" required 
                                    class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                                <option value="Disponible" selected>Disponible</option>
                                <option value="Ocupada">Ocupada</option>
                                <option value="Reservada">Reservada</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                        </div>

                        <!-- Ubicación -->
                        <div class="space-y-1">
                            <label for="ubicacion" class="font-bold text-[#2c1d11] block">Ubicación o Zona</label>
                            <input type="text" name="ubicacion" id="ubicacion" 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" 
                                   placeholder="Ej. Salón Principal, Terraza, VIP">
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                                Registrar Mesa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Mesa (AlpineJS) -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-gray-200 shadow-xl space-y-4 text-xs" @click.away="editModalOpen = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Editar Mesa</span>
                    </h4>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" :action="editFormAction" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Número -->
                    <div class="space-y-1">
                        <label for="edit_numero" class="font-bold text-[#2c1d11] block">Número o Código</label>
                        <input type="text" name="numero" id="edit_numero" x-model="editNumero" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Capacidad -->
                    <div class="space-y-1">
                        <label for="edit_capacidad" class="font-bold text-[#2c1d11] block">Capacidad (Personas)</label>
                        <input type="number" name="capacidad" id="edit_capacidad" x-model="editCapacidad" min="1" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Estado -->
                    <div class="space-y-1">
                        <label for="edit_estado" class="font-bold text-[#2c1d11] block">Estado</label>
                        <select name="estado" id="edit_estado" x-model="editEstado" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>

                    <!-- Ubicación -->
                    <div class="space-y-1">
                        <label for="edit_ubicacion" class="font-bold text-[#2c1d11] block">Ubicación o Zona</label>
                        <input type="text" name="ubicacion" id="edit_ubicacion" x-model="editUbicacion"
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
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
