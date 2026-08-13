@php
    // Obtener cantidades reales desde la base de datos de manera dinámica
    $rolCliente = \App\Models\Role::where('name', 'like', '%Cliente%')->first();
    $rolMesero = \App\Models\Role::where('name', 'like', '%Mesero%')->first();

    $cantClientes = $rolCliente ? \App\Models\User::where('rol_id', $rolCliente->id)->count() : 0;
    $cantMeseros = $rolMesero ? \App\Models\User::where('rol_id', $rolMesero->id)->count() : 0;
    $cantProductos = \App\Models\Producto::count();
    $cantMesas = \App\Models\Mesa::count();
    $cantReservas = \App\Models\Reserva::count();
    $cantPedidos = \App\Models\Pedido::count();
@endphp

<x-admin-layout>
    <div class="space-y-6">
        <!-- Banner de Bienvenida Estilo Charcoal/Gold -->
        <div class="flex flex-col lg:flex-row items-stretch justify-between gap-6">
            <div class="flex-1 flex flex-col justify-center">
                <span class="text-[11px] text-[#d4af37] font-bold uppercase tracking-widest block mb-1">Panel Administrativo</span>
                <h3 class="text-3xl font-bold tracking-tight text-[#2c1d11] mb-2">Bienvenido, Administrador</h3>
                <p class="text-sm text-gray-500 font-light mb-4">Gestiona clientes, meseros, productos, mesas, reservas y pedidos de Restaurante La Tribu.</p>
            </div>

            <!-- Card de La Tribu a la derecha -->
            <div class="w-full lg:w-64 bg-[#121619] rounded-2xl p-5 border border-[#d4af37]/20 flex flex-col items-center justify-center text-center shadow-md">
                <div class="w-14 h-14 rounded-2xl bg-[#d4af37] flex items-center justify-center text-[#121619] mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <h4 class="text-md font-bold text-[#d4af37] tracking-widest">LA TRIBU</h4>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">Sistema de Gestión</p>
            </div>
        </div>

        <!-- Grid de Métricas (6 Tarjetas con Icono Naranja/Gold y Fondo Blanco/Crema) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- CLIENTES -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Clientes</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantClientes }}</h4>
                </div>
            </div>

            <!-- MESEROS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Meseros</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantMeseros }}</h4>
                </div>
            </div>

            <!-- PRODUCTOS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Productos</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantProductos }}</h4>
                </div>
            </div>

            <!-- MESAS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Mesas</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantMesas }}</h4>
                </div>
            </div>

            <!-- RESERVAS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Reservas</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantReservas }}</h4>
                </div>
            </div>

            <!-- PEDIDOS -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 flex items-center gap-4 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#d4af37] text-[#121619] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Pedidos</span>
                    <h4 class="text-2xl font-bold tracking-tight text-[#2c1d11]">{{ $cantPedidos }}</h4>
                </div>
            </div>
        </div>

        <!-- Sección Inferior (Accesos Rápidos y Estado del Sistema) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Accesos rápidos -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 space-y-4 shadow-sm">
                <div>
                    <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Accesos rápidos</span>
                    </h4>
                    <p class="text-[10px] text-gray-400">Administra las secciones principales</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <a href="{{ route('admin.menu') }}" class="p-4 rounded-xl bg-[#fdfbf7] hover:bg-[#FAF4EB] border border-gray-200 flex flex-col items-center justify-center text-center group transition">
                        <div class="w-8 h-8 rounded-lg bg-[#FAF4EB] text-[#d4af37] flex items-center justify-center mb-2 group-hover:scale-110 transition border border-[#d4af37]/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-[#2c1d11]">Gestionar menú</span>
                    </a>

                    <a href="{{ route('admin.mesas') }}" class="p-4 rounded-xl bg-[#fdfbf7] hover:bg-[#FAF4EB] border border-gray-200 flex flex-col items-center justify-center text-center group transition">
                        <div class="w-8 h-8 rounded-lg bg-[#FAF4EB] text-[#d4af37] flex items-center justify-center mb-2 group-hover:scale-110 transition border border-[#d4af37]/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-[#2c1d11]">Ver mesas</span>
                    </a>
                </div>
            </div>

            <!-- Estado del sistema -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 space-y-4 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Estado del sistema</span>
                        </h4>
                        <p class="text-[10px] text-gray-400">Información general del servidor</p>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-semibold text-green-700 bg-green-50 px-3.5 py-2.5 rounded-xl border border-green-200 w-fit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Base de datos conectada</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <span class="text-[9px] text-gray-400 uppercase tracking-widest font-bold block leading-none mb-1">Fecha del servidor</span>
                    <span class="text-sm font-bold text-[#2c1d11] font-mono">{{ date('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
