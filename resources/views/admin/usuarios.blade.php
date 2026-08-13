@php
    // Obtener los datos inyectados por el controlador
    // $usuarios, $roles
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Gestión de Usuarios') }}
    </x-slot>

    <!-- Envoltorio Alpine.js para CRUD, Búsqueda, Filtro de Roles y Modales de Usuarios -->
    <div x-data="{ 
            registerModalOpen: false,
            editModalOpen: false, 
            deleteModalOpen: false,
            deleteFormAction: '',
            deleteUserName: '',
            editFormAction: '', 
            editName: '', 
            editLastName: '', 
            editEmail: '', 
            editPhone: '', 
            editRolId: '', 
            editActive: true, 
            searchQuery: '', 
            selectedRol: '',
            currentPage: 1,
            usuarios: {{ $usuarios->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'last_name' => $u->last_name,
                'email' => $u->email,
                'phone' => $u->phone ?? 'Sin especificar',
                'rol_id' => $u->rol_id,
                'rol_nombre' => $u->role?->name ?? 'Sin Rol',
                'active' => $u->active
            ])->toJson() }},
            getFilteredUsuarios() {
                return this.usuarios.filter(u => {
                    const matchesSearch = this.searchQuery === '' || u.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || u.last_name.toLowerCase().includes(this.searchQuery.toLowerCase()) || u.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesRol = this.selectedRol === '' || u.rol_id.toString() === this.selectedRol.toString();
                    return matchesSearch && matchesRol;
                });
            },
            getPaginatedUsuarios() {
                const start = (this.currentPage - 1) * 6;
                return this.getFilteredUsuarios().slice(start, start + 6);
            },
            totalPages() {
                return Math.ceil(this.getFilteredUsuarios().length / 6) || 1;
            }
         }"
         x-init="$watch('searchQuery', () => currentPage = 1); $watch('selectedRol', () => currentPage = 1);"
         class="space-y-6">
        
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• ADMINISTRACIÓN DE PERSONAL •</span>
                <h3 class="text-2xl font-serif italic text-white">Listado de Usuarios</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Registra y administra los perfiles de administradores, meseros y clientes del restaurante.</p>
            </div>
            <!-- Total count on the right -->
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Total Usuarios</span>
                <span class="text-2xl font-bold font-mono text-white">{{ $usuarios->count() }}</span>
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

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Botón Agregar Usuario arriba del listado -->
        <div class="flex justify-end">
            <button @click="registerModalOpen = true" class="px-4 py-2 bg-[#d4af37] hover:bg-[#c29d2e] text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition-all duration-200 shadow-sm hover:shadow flex items-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#121619]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Agregar Usuario</span>
            </button>
        </div>

        <!-- TABLA DE USUARIOS (Ancho Completo) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between min-h-[500px]">
            <div>
                <!-- Filtros y Cabecera -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cuentas Registradas</span>
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
                                   placeholder="Buscar por nombre, apellido o correo...">
                        </div>

                        <select x-model="selectedRol" 
                                class="px-3 py-2 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tabla de Cuentas -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                <th class="py-3">Usuario</th>
                                <th class="py-3">Correo Electrónico</th>
                                <th class="py-3">Teléfono</th>
                                <th class="py-3">Rol asignado</th>
                                <th class="py-3">Estado</th>
                                <th class="py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs">
                            <template x-for="usuario in getPaginatedUsuarios()" :key="usuario.id">
                                <tr>
                                    <td class="py-3.5 pr-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-[#FAF4EB] text-[#d4af37] flex items-center justify-center font-bold text-xs shrink-0 border border-gray-100"
                                                 x-text="usuario.name.substring(0,2).toUpperCase()">
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-[#2c1d11]" x-text="usuario.name + ' ' + usuario.last_name"></h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 text-gray-500 font-mono" x-text="usuario.email"></td>
                                    <td class="py-3.5 text-gray-500" x-text="usuario.phone"></td>
                                    <td class="py-3.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                              :class="{
                                                  'bg-purple-50 text-purple-700': usuario.rol_nombre === 'Admin' || usuario.rol_nombre === 'Administrador',
                                                  'bg-blue-50 text-blue-700': usuario.rol_nombre === 'Mesero',
                                                  'bg-gray-100 text-gray-700': usuario.rol_nombre === 'Cliente'
                                              }"
                                              x-text="usuario.rol_nombre"></span>
                                    </td>
                                    <td class="py-3.5">
                                        <!-- Botón para alternar estado activo/inactivo con un click -->
                                        <form method="POST" :action="'/admin/usuarios/' + usuario.id + '/toggle'" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    title="Alternar estado"
                                                    :class="usuario.active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'"
                                                    class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider transition hover:opacity-80">
                                                <span x-text="usuario.active ? 'Activo' : 'Inactivo'"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-3.5 text-right whitespace-nowrap">
                                        <!-- Editar -->
                                        <button @click="
                                            editName = usuario.name;
                                            editLastName = usuario.last_name;
                                            editEmail = usuario.email;
                                            editPhone = usuario.phone;
                                            editRolId = usuario.rol_id;
                                            editActive = usuario.active;
                                            editFormAction = '/admin/usuarios/' + usuario.id;
                                            editModalOpen = true;
                                        " class="inline-flex items-center justify-center p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition mr-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <!-- Eliminar -->
                                        <button @click="
                                            deleteUserName = usuario.name + ' ' + usuario.last_name;
                                            deleteFormAction = '/admin/usuarios/' + usuario.id;
                                            deleteModalOpen = true;
                                        " class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <template x-if="getFilteredUsuarios().length === 0">
                        <div class="text-center py-12 text-gray-400">
                            <p class="text-xs">No se encontraron cuentas registradas.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Controles de Paginación Fijos abajo -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                <span class="text-[10px] text-gray-400 font-medium">
                    Mostrando página <span x-text="currentPage" class="font-bold text-[#2c1d11]"></span> de <span x-text="totalPages()" class="font-bold text-[#2c1d11]"></span> (<span x-text="getFilteredUsuarios().length" class="font-bold text-[#2c1d11]"></span> usuarios filtrados)
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

        <!-- MODAL FLOTANTE DE CREACIÓN DE USUARIO (AlpineJS) -->
        <div x-show="registerModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-gray-200 shadow-2xl space-y-4 text-xs" @click.away="registerModalOpen = false"
                 x-show="registerModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Registrar Nuevo Usuario</span>
                    </h4>
                    <button @click="registerModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-4">
                    @csrf

                    <!-- Nombres y Apellidos -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="name" class="font-bold text-[#2c1d11] block">Nombre(s)</label>
                            <input type="text" name="name" id="name" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="Ej. Juan">
                        </div>
                        <div class="space-y-1">
                            <label for="last_name" class="font-bold text-[#2c1d11] block">Apellido(s)</label>
                            <input type="text" name="last_name" id="last_name" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="Ej. Pérez">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="email" class="font-bold text-[#2c1d11] block">Correo Electrónico</label>
                        <input type="email" name="email" id="email" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="juan.perez@ejemplo.com">
                    </div>

                    <!-- Telefono -->
                    <div class="space-y-1">
                        <label for="phone" class="font-bold text-[#2c1d11] block">Teléfono (Opcional)</label>
                        <input type="text" name="phone" id="phone" 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="Ej. 3001234567">
                    </div>

                    <!-- Rol -->
                    <div class="space-y-1">
                        <label for="rol_id" class="font-bold text-[#2c1d11] block">Rol Asignado</label>
                        <select name="rol_id" id="rol_id" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            <option value="" disabled selected>Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contraseña -->
                    <div class="space-y-1">
                        <label for="password" class="font-bold text-[#2c1d11] block">Contraseña</label>
                        <input type="password" name="password" id="password" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="••••••••">
                    </div>

                    <!-- Acciones -->
                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" @click="registerModalOpen = false" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition">
                            Cancelar
                        </button>
                        <button type="submit" class="w-1/2 py-2.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL FLOTANTE DE EDICIÓN DE USUARIO (AlpineJS) -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-gray-200 shadow-2xl space-y-4 text-xs" @click.away="editModalOpen = false"
                 x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Editar Cuenta de Usuario</span>
                    </h4>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" :action="editFormAction" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nombres y Apellidos -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="edit_name" class="font-bold text-[#2c1d11] block">Nombre(s)</label>
                            <input type="text" name="name" id="edit_name" x-model="editName" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                        </div>
                        <div class="space-y-1">
                            <label for="edit_last_name" class="font-bold text-[#2c1d11] block">Apellido(s)</label>
                            <input type="text" name="last_name" id="edit_last_name" x-model="editLastName" required 
                                   class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="edit_email" class="font-bold text-[#2c1d11] block">Correo Electrónico</label>
                        <input type="email" name="email" id="edit_email" x-model="editEmail" required 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Telefono -->
                    <div class="space-y-1">
                        <label for="edit_phone" class="font-bold text-[#2c1d11] block">Teléfono (Opcional)</label>
                        <input type="text" name="phone" id="edit_phone" x-model="editPhone"
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                    </div>

                    <!-- Rol -->
                    <div class="space-y-1">
                        <label for="edit_rol_id" class="font-bold text-[#2c1d11] block">Rol Asignado</label>
                        <select name="rol_id" id="edit_rol_id" x-model="editRolId" required 
                                class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contraseña (Nueva) -->
                    <div class="space-y-1">
                        <label for="edit_password" class="font-bold text-[#2c1d11] block">Nueva Contraseña (Dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" id="edit_password" 
                               class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition" placeholder="••••••••">
                    </div>

                    <!-- Disponible / Activo -->
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="active" id="edit_active" value="1" :checked="editActive" 
                               class="rounded text-[#d4af37] focus:ring-[#d4af37] border-gray-300">
                        <label for="edit_active" class="font-bold text-[#2c1d11]">Cuenta Activa</label>
                    </div>

                    <!-- Acciones -->
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
        <!-- MODAL FLOTANTE DE CONFIRMACIÓN DE ELIMINACIÓN (AlpineJS) -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm border border-gray-200 shadow-2xl text-center space-y-4 text-xs" @click.away="deleteModalOpen = false"
                 x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto border-2 border-red-100 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div class="space-y-1">
                    <h4 class="text-md font-bold text-[#2c1d11]">¿Confirmar Eliminación?</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">
                        ¿Estás seguro de que deseas eliminar permanentemente la cuenta de <span class="font-bold text-[#2c1d11]" x-text="deleteUserName"></span>? Esta acción no se puede deshacer.
                    </p>
                </div>

                <form method="POST" :action="deleteFormAction" class="pt-2 flex items-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition">
                        Cancelar
                    </button>
                    <button type="submit" class="w-1/2 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
