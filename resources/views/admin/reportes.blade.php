<x-admin-layout>
    <x-slot name="header">
        {{ __('Reportes de Ventas') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Banner Informativo -->
        <div class="bg-[#121619] rounded-2xl p-6 text-white shadow-md border border-[#d4af37]/20 flex justify-between items-center">
            <div>
                <span class="text-[9px] tracking-[0.3em] text-[#d4af37] font-bold uppercase block mb-1.5">• MÉTRICAS Y RENDIMIENTO •</span>
                <h3 class="text-2xl font-serif italic text-white">Reportes y Estadísticas</h3>
                <p class="text-xs text-gray-400 font-light mt-1">Monitorea los ingresos generados, volumen de pedidos, reservas y preferencias de los clientes en un rango de fechas.</p>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[9px] text-[#d4af37] uppercase tracking-widest block font-bold leading-none mb-1">Moneda</span>
                <span class="text-2xl font-bold font-mono text-white">COP ($)</span>
            </div>
        </div>

        <!-- Filtro por Rango de Fechas -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
            <form method="GET" action="{{ route('admin.reportes') }}" class="grid grid-cols-1 md:grid-cols-4 items-end gap-4 text-xs w-full max-w-4xl">
                <div class="space-y-1">
                    <label for="fecha_inicio" class="font-bold text-[#2c1d11] block">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}" required
                           class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                </div>
                <div class="space-y-1">
                    <label for="fecha_fin" class="font-bold text-[#2c1d11] block">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}" required
                           class="w-full px-3.5 py-2.5 bg-[#fdfbf7] border border-gray-200 rounded-xl focus:outline-none focus:border-[#d4af37] text-xs transition">
                </div>
                <div>
                    <button type="submit" class="w-full h-[42px] bg-[#d4af37] hover:bg-[#c29d2e] text-[#121619] font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#121619]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filtrar Reporte</span>
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.reportes.pdf', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="w-full h-[42px] bg-[#121619] border border-[#d4af37]/45 hover:border-[#d4af37] hover:bg-[#1a2023] text-[#d4af37] font-bold rounded-xl text-xs uppercase tracking-wider transition-all duration-250 shadow-sm hover:shadow flex items-center justify-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Exportar PDF</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tarjetas de Métricas Clave -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Ingresos -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center border border-green-200/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Ventas Entregadas</span>
                    <span class="text-xl font-bold font-mono text-[#2c1d11]">${{ number_format($ventasTotales, 2) }}</span>
                </div>
            </div>

            <!-- Pedidos -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Pedidos Totales</span>
                    <span class="text-xl font-bold font-mono text-[#2c1d11]">{{ $pedidosCount }} comandas</span>
                </div>
            </div>

            <!-- Reservas -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#d4af37] flex items-center justify-center border border-amber-200/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Reservas Registradas</span>
                    <span class="text-xl font-bold font-mono text-[#2c1d11]">{{ $reservasCount }} fechas</span>
                </div>
            </div>

            <!-- Mesas -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Distribución de Mesas</span>
                    <span class="text-xl font-bold font-mono text-[#2c1d11]">{{ $mesasCount }} mesas</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- VENTAS POR CATEGORÍA (Custom Progress Bars) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
                <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 border-b border-gray-100 pb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <span>Ventas por Categoría de Comida</span>
                </h4>

                @if($ventasPorCategoria->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs">
                        <p>No se registran ventas para las categorías en este rango de fechas.</p>
                    </div>
                @else
                    <div class="space-y-4 pt-2 text-xs">
                        @php
                            $granTotalCategorias = $ventasPorCategoria->sum('total');
                        @endphp
                        @foreach($ventasPorCategoria as $c)
                            @php
                                $porcentaje = $granTotalCategorias > 0 ? ($c->total / $granTotalCategorias) * 100 : 0;
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-[#2c1d11]">{{ $c->categoria }}</span>
                                    <div class="font-mono text-right">
                                        <span class="font-bold text-[#2c1d11]">${{ number_format($c->total, 2) }}</span>
                                        <span class="text-gray-400 font-light text-[10px] ml-1">({{ number_format($porcentaje, 1) }}%)</span>
                                    </div>
                                </div>
                                <!-- Barra de progreso personalizada -->
                                <div class="w-full bg-[#fdfbf7] h-3.5 rounded-full overflow-hidden border border-gray-100">
                                    <div class="bg-[#d4af37] h-full rounded-full transition-all duration-500" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- TOP 5 DE PRODUCTOS MÁS VENDIDOS -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
                <h4 class="text-md font-bold text-[#2c1d11] flex items-center gap-2 border-b border-gray-100 pb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>Platillos Favoritos (Top 5)</span>
                </h4>

                @if($productosMasVendidos->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs">
                        <p>No se registran comidas vendidas en este rango de fechas.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-gray-100 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                    <th class="py-2.5">Platillo</th>
                                    <th class="py-2.5 text-center">Unidades</th>
                                    <th class="py-2.5 text-right">Recaudado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($productosMasVendidos as $index => $p)
                                    <tr>
                                        <td class="py-3 pr-2 flex items-center gap-2">
                                            <!-- Puesto en Ranking -->
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0 border"
                                                  class="{{ $index === 0 ? 'bg-amber-50 border-[#d4af37]/30 text-[#d4af37]' : 'bg-gray-50 border-gray-100 text-gray-500' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="font-bold text-[#2c1d11]">{{ $p->nombre }}</span>
                                        </td>
                                        <td class="py-3 text-center text-gray-500 font-semibold font-mono">{{ $p->cantidad }} uds</td>
                                        <td class="py-3 text-right font-bold text-[#2c1d11] font-mono">${{ number_format($p->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
