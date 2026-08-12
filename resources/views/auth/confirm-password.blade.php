<x-guest-layout>
    
    <!-- Lado Izquierdo (Panel de Bienvenida en fondo oscuro premium) -->
    <div class="p-8 sm:p-12 flex flex-col justify-between bg-tribu-darkBg text-white relative min-h-[450px] md:min-h-[500px]">
        
        <!-- Botón de Inicio / Volver -->
        <div>
            <a href="/" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-tribu-gold transition tracking-widest uppercase">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Volver al Inicio</span>
            </a>
        </div>

        <!-- Contenido Central -->
        <div class="my-auto space-y-6 text-center flex flex-col items-center">
            <!-- Logotipo central vintage -->
            <div class="flex flex-col items-center justify-center text-center">
                <span class="text-[9px] tracking-[0.3em] text-tribu-gold/80 uppercase font-semibold">• LA RECETA ORIGINAL •</span>
                <h1 class="text-3xl font-light tracking-[0.2em] text-white font-serif my-1 leading-none">LA TRIBU</h1>
                <div class="flex items-center justify-center gap-2 my-1">
                    <span class="h-[1px] w-6 bg-tribu-gold/40"></span>
                    <span class="text-[9px] tracking-widest text-gray-400 uppercase">EST. 2026</span>
                    <span class="h-[1px] w-6 bg-tribu-gold/40"></span>
                </div>
                <span class="text-[9px] tracking-[0.35em] text-tribu-gold uppercase font-bold">FAMILY RESTAURANT</span>
            </div>

            <!-- Descripción -->
            <div class="space-y-3">
                <p class="text-xs text-gray-300 font-light leading-relaxed max-w-xs mx-auto italic font-serif">
                    Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
                </p>
            </div>

            <div class="w-12 h-[1px] bg-tribu-gold/30"></div>
        </div>

        <!-- Pie de panel -->
        <div class="text-center text-[10px] text-gray-500 uppercase tracking-wider">
            Área de Seguridad Protegida
        </div>
    </div>

    <!-- Lado Derecho (Formulario / Fondo Crema Claro) -->
    <div class="p-8 sm:p-12 bg-[#fdfbf7] text-tribu-dark flex flex-col justify-between min-h-[450px] md:min-h-[500px]">
        
        <!-- Logo de la marca -->
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-2xl bg-tribu-darkBg p-2 flex items-center justify-center border border-white/10 shadow-md">
                <img src="{{ asset('images/la_tribu_logo.png') }}" alt="La Tribu Logo" class="max-h-full max-w-full object-contain rounded-xl">
            </div>
        </div>

        <!-- Formulario e Inputs -->
        <div class="my-auto space-y-5 pt-4">
            <div class="text-center space-y-1">
                <h3 class="text-2xl font-serif tracking-wide text-tribu-dark italic">Confirmar Clave</h3>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-relaxed">
                    Confirma tu identidad para continuar
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <!-- Password -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           placeholder="Contraseña"
                           class="block w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-tribu-gold focus:border-transparent bg-white text-tribu-dark placeholder-gray-400 text-xs font-medium transition">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 bg-tribu-darkBg hover:bg-neutral-800 text-tribu-gold font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest">
                        <span>Confirmar &rarr;</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="h-4"></div>

    </div>

</x-guest-layout>
