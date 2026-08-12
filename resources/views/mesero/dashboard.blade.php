<x-mesero-layout>
    <x-slot name="header">
        {{ __('Panel de Mesero / Atención') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-8 text-gray-900">
                    <h3 class="text-xl font-serif italic mb-2">¡Hola, {{ Auth::user()->name }}!</h3>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Has ingresado como Mesero de La Tribu. Desde tu menú lateral puedes atender mesas y gestionar pedidos en tiempo real.</p>
                </div>
            </div>
        </div>
    </div>
</x-mesero-layout>
