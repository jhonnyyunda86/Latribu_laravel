<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Restaurante La Tribu') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            tribu: {
                                gold: '#d4af37',
                                darkBg: '#121619',
                                orange: '#F53003',
                                dark: '#1b1b18',
                                cream: '#FAF4EB',
                                fontColor: '#2c1d11'
                            }
                        },
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                            serif: ['Cormorant Garamond', 'serif'],
                        }
                    }
                }
            }
        </script>
        <style>
            html {
                scroll-behavior: smooth;
            }
            body {
                font-family: 'Outfit', sans-serif;
            }
            .zigzag {
                position: relative;
            }
            .zigzag::after {
                content: "";
                position: absolute;
                bottom: -10px;
                left: 0;
                width: 100%;
                height: 10px;
                background-image: linear-gradient(135deg, #121619 25%, transparent 25%), 
                                  linear-gradient(225deg, #121619 25%, transparent 25%);
                background-position: 0 0, 5px 0;
                background-size: 10px 10px;
                background-repeat: repeat-x;
                z-index: 30;
            }
        </style>
    </head>
    <body class="bg-[#fdfbf7] text-[#2c1d11] antialiased">
        
        <!-- Header / Navbar estilo Vincent Pizza -->
        <header class="bg-tribu-darkBg text-white px-6 lg:px-16 pt-8 pb-4 zigzag z-40 relative">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 md:gap-4">
                
                <!-- Datos de contacto (Izquierda) -->
                <div class="flex items-center gap-3 text-left w-full md:w-1/3 justify-start">
                    <div class="text-tribu-gold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="font-sans text-xs tracking-wider">
                        <div class="text-white font-bold text-sm tracking-widest">+57 123 4567</div>
                        <div class="text-gray-400 text-[10px] mt-0.5 uppercase">8:00 am - 11:30 pm</div>
                    </div>
                </div>

                <!-- Logotipo central vintage (Centro) -->
                <div class="flex flex-col items-center justify-center text-center w-full md:w-1/3">
                    <span class="text-[9px] tracking-[0.3em] text-tribu-gold/80 uppercase font-semibold">• LA RECETA ORIGINAL •</span>
                    <h1 class="text-3xl font-light tracking-[0.2em] text-white font-serif my-0.5 leading-none">LA TRIBU</h1>
                    <div class="flex items-center justify-center gap-2 my-1">
                        <span class="h-[1px] w-6 bg-tribu-gold/40"></span>
                        <span class="text-[9px] tracking-widest text-gray-400 uppercase">EST. 2026</span>
                        <span class="h-[1px] w-6 bg-tribu-gold/40"></span>
                    </div>
                    <span class="text-[9px] tracking-[0.35em] text-tribu-gold uppercase font-bold">FAMILY RESTAURANT</span>
                </div>

                <!-- Acceso al sistema / Registro (Derecha) -->
                <div class="flex items-center gap-3 w-full md:w-1/3 justify-end">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="group flex items-center gap-3 hover:opacity-90 transition">
                                <div class="text-right font-sans text-xs hidden sm:block">
                                    <div class="text-white font-bold tracking-widest uppercase">Dashboard</div>
                                    <div class="text-tribu-gold text-[10px] uppercase">Mi Panel</div>
                                </div>
                                <div class="bg-tribu-gold/10 border border-tribu-gold/30 text-tribu-gold p-2.5 rounded-full group-hover:bg-tribu-gold group-hover:text-tribu-darkBg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 border border-white/20 hover:border-tribu-gold text-white hover:text-tribu-gold rounded-full font-sans font-bold text-xs uppercase tracking-widest transition">
                                Ingresar
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2 bg-tribu-gold hover:bg-yellow-600 text-tribu-darkBg rounded-full font-sans font-bold text-xs uppercase tracking-widest transition shadow-md">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

            </div>

            <!-- Navegación central (Menú de Links) -->
            <nav class="flex items-center justify-center gap-8 mt-6 pt-4 border-t border-white/10 text-xs font-bold text-gray-300 uppercase tracking-[0.25em] font-sans max-w-7xl mx-auto">
                <a href="#inicio" class="hover:text-tribu-gold transition duration-300 relative py-1 group">
                    Inicio
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-tribu-gold rounded-full scale-0 group-hover:scale-100 transition duration-300"></span>
                </a>
                <a href="#servicios" class="hover:text-tribu-gold transition duration-300 relative py-1 group">
                    Servicios
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-tribu-gold rounded-full scale-0 group-hover:scale-100 transition duration-300"></span>
                </a>
                <a href="#menu" class="hover:text-tribu-gold transition duration-300 relative py-1 group">
                    Menú
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-tribu-gold rounded-full scale-0 group-hover:scale-100 transition duration-300"></span>
                </a>
                <a href="#nosotros" class="hover:text-tribu-gold transition duration-300 relative py-1 group">
                    Nosotros
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-tribu-gold rounded-full scale-0 group-hover:scale-100 transition duration-300"></span>
                </a>
            </nav>
        </header>

        <!-- Hero Carousel Section -->
        <section id="inicio" class="relative overflow-hidden h-[580px] bg-tribu-darkBg">
            <!-- Slides Container -->
            <div id="hero-slider" class="relative w-full h-full">
                <!-- Slide 1 -->
                <div class="hero-slide absolute inset-0 opacity-100 transition-opacity duration-1000 ease-in-out z-10">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/pizza_artesanal.png') }}');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-tribu-darkBg via-black/60 to-black/50"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                        <span class="text-xs md:text-sm font-semibold tracking-[0.3em] text-tribu-gold uppercase mb-3 animate-pulse">EL AUTÉNTICO SABOR RÚSTICO</span>
                        <h2 class="text-4xl md:text-7xl font-serif text-white uppercase tracking-[0.1em] font-light mb-4 max-w-4xl">PIZZA ARTESANAL.</h2>
                        <p class="text-sm md:text-lg text-gray-300 font-light max-w-2xl font-serif italic">Masa crocante horneada a la leña, queso fundido de primera calidad e ingredientes seleccionados.</p>
                    </div>
                </div>
                
                <!-- Slide 2 -->
                <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out z-0">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/hamburguesa_tribal.png') }}');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-tribu-darkBg via-black/60 to-black/50"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                        <span class="text-xs md:text-sm font-semibold tracking-[0.3em] text-tribu-gold uppercase mb-3">RECETA EXCLUSIVA DE LA CASA</span>
                        <h2 class="text-4xl md:text-7xl font-serif text-white uppercase tracking-[0.1em] font-light mb-4 max-w-4xl">HAMBURGUESA TRIBAL.</h2>
                        <p class="text-sm md:text-lg text-gray-300 font-light max-w-2xl font-serif italic">Carne tierna y jugosa sazonada con finas hierbas, vegetales frescos y la mística salsa secreta.</p>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out z-0">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/plato_especial.png') }}');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-tribu-darkBg via-black/60 to-black/50"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                        <span class="text-xs md:text-sm font-semibold tracking-[0.3em] text-tribu-gold uppercase mb-3">EXPERIENCIA PARA COMPARTIR</span>
                        <h2 class="text-4xl md:text-7xl font-serif text-white uppercase tracking-[0.1em] font-light mb-4 max-w-4xl">PLATO ESPECIAL.</h2>
                        <p class="text-sm md:text-lg text-gray-300 font-light max-w-2xl font-serif italic">Una combinación sublime de carnes seleccionadas y guarniciones artesanales preparadas con amor.</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button onclick="prevSlide()" class="absolute left-6 top-1/2 -translate-y-1/2 z-20 text-white/50 hover:text-white bg-black/20 hover:bg-black/40 p-3 rounded-full border border-white/10 hover:border-white/30 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button onclick="nextSlide()" class="absolute right-6 top-1/2 -translate-y-1/2 z-20 text-white/50 hover:text-white bg-black/20 hover:bg-black/40 p-3 rounded-full border border-white/10 hover:border-white/30 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Slide Indicators -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-3">
                <button onclick="goToSlide(0)" class="slide-indicator w-2.5 h-2.5 rounded-full bg-white transition duration-300"></button>
                <button onclick="goToSlide(1)" class="slide-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/70 transition duration-300"></button>
                <button onclick="goToSlide(2)" class="slide-indicator w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/70 transition duration-300"></button>
            </div>
        </section>

        <!-- Servicios del Sistema -->
        <section id="servicios" class="bg-[#FAF4EB]/60 py-24 px-6 border-b border-[#FAF4EB]">
            <div class="max-w-7xl mx-auto text-center space-y-4 mb-20">
                <span class="text-tribu-gold text-xs font-black uppercase tracking-[0.25em]">• NUESTRAS VIRTUDES •</span>
                <h2 class="text-3xl md:text-5xl font-serif text-tribu-dark italic tracking-wide">Todo tu restaurante en una sola plataforma</h2>
                <div class="w-12 h-[1px] bg-tribu-gold mx-auto mt-4"></div>
                <p class="text-[#5c4938] max-w-2xl mx-auto font-light mt-2">Controla las operaciones principales de La Tribu desde una interfaz clara, rápida y profesional.</p>
            </div>

            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Gestión de Pedidos -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 border border-gray-100 space-y-6">
                    <div class="bg-[#F53003]/5 text-[#F53003] w-14 h-14 rounded-full flex items-center justify-center text-3xl">
                        🍔
                    </div>
                    <h3 class="text-xl font-bold font-serif text-tribu-dark">Gestión de pedidos</h3>
                    <p class="text-[#5c4938] text-sm leading-relaxed font-light">Administra pedidos, estados, productos y atención al cliente de manera eficiente.</p>
                </div>

                <!-- Control de Inventario -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 border border-gray-100 space-y-6">
                    <div class="bg-[#FBBF24]/5 text-tribu-gold w-14 h-14 rounded-full flex items-center justify-center text-3xl">
                        📦
                    </div>
                    <h3 class="text-xl font-bold font-serif text-tribu-dark">Control de inventario</h3>
                    <p class="text-[#5c4938] text-sm leading-relaxed font-light">Consulta existencias, productos disponibles y evita pérdidas en tu restaurante.</p>
                </div>

                <!-- Usuarios y Roles -->
                <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 border border-gray-100 space-y-6">
                    <div class="bg-purple-50 text-purple-600 w-14 h-14 rounded-full flex items-center justify-center text-3xl">
                        👥
                    </div>
                    <h3 class="text-xl font-bold font-serif text-tribu-dark">Usuarios y roles</h3>
                    <p class="text-[#5c4938] text-sm leading-relaxed font-light">Permite el acceso seguro para administradores, empleados y clientes registrados.</p>
                </div>
            </div>
        </section>

        <!-- Especialidades (Menú) -->
        <section id="menu" class="py-24 px-6 max-w-7xl mx-auto">
            <div class="text-center space-y-4 mb-20">
                <span class="text-tribu-gold text-xs font-black uppercase tracking-[0.25em]">• NUESTRAS ESPECIALIDADES •</span>
                <h2 class="text-3xl md:text-5xl font-serif text-tribu-dark italic tracking-wide">Sabores de La Tribu</h2>
                <div class="w-12 h-[1px] bg-tribu-gold mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Hamburguesa Tribal -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="h-72 overflow-hidden relative">
                        <img src="{{ asset('images/hamburguesa_tribal.png') }}" alt="Hamburguesa Tribal" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    </div>
                    <div class="p-8 space-y-3">
                        <h3 class="text-xl font-bold font-serif text-tribu-dark">Hamburguesa Tribal</h3>
                        <p class="text-[#5c4938] text-sm leading-relaxed font-light">Carne jugosa, vegetales frescos y salsa especial de la casa.</p>
                    </div>
                </div>

                <!-- Pizza Artesanal -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="h-72 overflow-hidden relative">
                        <img src="{{ asset('images/pizza_artesanal.png') }}" alt="Pizza Artesanal" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    </div>
                    <div class="p-8 space-y-3">
                        <h3 class="text-xl font-bold font-serif text-tribu-dark">Pizza Artesanal</h3>
                        <p class="text-[#5c4938] text-sm leading-relaxed font-light">Masa crocante, queso fundido e ingredientes seleccionados.</p>
                    </div>
                </div>

                <!-- Plato Especial -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="h-72 overflow-hidden relative">
                        <img src="{{ asset('images/plato_especial.png') }}" alt="Plato Especial" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    </div>
                    <div class="p-8 space-y-3">
                        <h3 class="text-xl font-bold font-serif text-tribu-dark">Plato Especial</h3>
                        <p class="text-[#5c4938] text-sm leading-relaxed font-light">Una combinación deliciosa para compartir en familia.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sobre Nosotros -->
        <section id="nosotros" class="bg-[#FAF4EB]/60 py-24 px-6 border-t border-[#FAF4EB]">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <!-- Imagen -->
                <div class="lg:col-span-6 relative">
                    <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white">
                        <img src="{{ asset('images/la_tribu_interior.png') }}" alt="Nosotros La Tribu" class="w-full h-96 object-cover">
                    </div>
                </div>

                <!-- Contenido de Texto -->
                <div class="lg:col-span-6 space-y-8">
                    <span class="text-tribu-gold text-xs font-black uppercase tracking-[0.25em]">• NUESTRA HISTORIA •</span>
                    <h2 class="text-3xl md:text-5xl font-serif text-tribu-dark italic tracking-wide">Una experiencia creada para compartir</h2>
                    <div class="w-12 h-[1px] bg-tribu-gold"></div>
                    <p class="text-[#5c4938] leading-relaxed font-light">
                        Restaurante La Tribu combina sabor, atención y tecnología. Este sistema está diseñado para mejorar la administración interna, facilitar el registro de usuarios y permitir una gestión más clara de los procesos del restaurante.
                    </p>

                    <!-- Lista de beneficios -->
                    <div class="space-y-6 pt-4">
                        <div class="flex items-start gap-4">
                            <div class="bg-green-50 text-green-600 p-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold font-serif text-tribu-dark">Procesos más rápidos</h4>
                                <p class="text-sm text-gray-500 font-light">Menos desorden y más control operativo en tiempo real.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-green-50 text-green-600 p-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold font-serif text-tribu-dark">Acceso seguro</h4>
                                <p class="text-sm text-gray-500 font-light">Ingreso mediante credenciales encriptadas para cada rol de usuario.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA / Footer -->
        <footer class="bg-tribu-darkBg text-white py-20 px-6 text-center space-y-8 relative">
            <div class="max-w-xl mx-auto space-y-6">
                <span class="text-tribu-gold text-xs font-black uppercase tracking-[0.25em]">• ÚNETE A LA TRIBU •</span>
                <h2 class="text-3xl md:text-5xl font-serif font-light tracking-wide">¿Listo para comenzar?</h2>
                <p class="text-gray-400 text-sm font-light">Regístrate en la plataforma y comienza a gestionar el restaurante con la máxima elegancia y velocidad.</p>
                <div class="pt-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-tribu-gold text-tribu-darkBg font-sans font-bold text-xs uppercase tracking-widest rounded-full hover:bg-yellow-600 transition shadow-lg">
                            <span>Registrarme ahora</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="text-[10px] text-gray-500 pt-12 border-t border-white/5 tracking-widest uppercase">
                &copy; {{ date('Y') }} Restaurante La Tribu. Todos los derechos reservados.
            </div>
        </footer>

        <!-- Carousel Script -->
        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.hero-slide');
            const indicators = document.querySelectorAll('.slide-indicator');
            let slideInterval = setInterval(nextSlide, 6000);

            function showSlide(index) {
                // Reset interval to avoid instant jump
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 6000);

                // Update slides opacity and z-index
                slides.forEach((slide, i) => {
                    if (i === index) {
                        slide.classList.remove('opacity-0', 'z-0');
                        slide.classList.add('opacity-100', 'z-10');
                    } else {
                        slide.classList.remove('opacity-100', 'z-10');
                        slide.classList.add('opacity-0', 'z-0');
                    }
                });

                // Update indicators styling
                indicators.forEach((indicator, i) => {
                    if (i === index) {
                        indicator.classList.remove('bg-white/40');
                        indicator.classList.add('bg-white');
                    } else {
                        indicator.classList.remove('bg-white');
                        indicator.classList.add('bg-white/40');
                    }
                });

                currentSlide = index;
            }

            function nextSlide() {
                let next = (currentSlide + 1) % slides.length;
                showSlide(next);
            }

            function prevSlide() {
                let prev = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prev);
            }

            function goToSlide(index) {
                showSlide(index);
            }
        </script>

        <!-- CHATBOT ASISTENTE VIRTUAL LA TRIBU -->
        <div x-data="tribuChatbot()" class="fixed bottom-6 right-6 z-50 font-sans text-[#2c1d11]">
            <!-- Botón flotante -->
            <button @click="toggleChat()" 
                    class="bg-[#d4af37] hover:bg-yellow-600 text-[#121619] p-4 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 active:scale-95 focus:outline-none relative">
                <span class="absolute -top-1.5 -right-1.5 bg-red-650 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md animate-bounce" x-show="unread">1</span>
                <!-- Icono de chat -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#121619]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </button>

            <!-- Ventana de Chat -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300 transform translate-y-4 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform translate-y-4 opacity-100"
                 x-transition:leave-end="translate-y-4 opacity-0"
                 class="absolute bottom-16 right-0 w-[340px] sm:w-[380px] bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-150 flex flex-col h-[480px]"
                 x-cloak>
                
                <!-- Encabezado del Chat -->
                <div class="bg-[#121619] text-white p-4 flex items-center justify-between border-b border-[#d4af37]/20 relative">
                    <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 w-24 h-24 bg-[#d4af37]/5 rounded-full blur-xl pointer-events-none"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#d4af37]/15 border border-[#d4af37]/40 flex items-center justify-center text-lg shadow-sm">
                            🤠
                        </div>
                        <div class="text-left">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#d4af37] leading-none mb-1">Asistente La Tribu</h4>
                            <span class="text-[9px] text-gray-400 font-light flex items-center gap-1.5 leading-none">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                En línea ahora
                            </span>
                        </div>
                    </div>
                    <button @click="toggleChat()" class="text-gray-400 hover:text-white transition font-bold text-lg">&times;</button>
                </div>

                <!-- Lista de Mensajes -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#fdfbf7] text-left" id="chat-messages-container">
                    <template x-for="msg in messages">
                        <div class="flex flex-col" :class="msg.sender === 'user' ? 'items-end' : 'items-start'">
                            <div :class="msg.sender === 'user' ? 'bg-[#121619] text-white rounded-2xl rounded-tr-none' : 'bg-white border border-gray-200 text-[#2c1d11] rounded-2xl rounded-tl-none'"
                                 class="max-w-[85%] p-3 text-xs shadow-sm leading-relaxed whitespace-pre-line">
                                <span x-html="msg.text"></span>
                            </div>
                            
                            <!-- Botones rápidos en el mensaje del bot -->
                            <template x-if="msg.buttons && msg.buttons.length > 0">
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    <template x-for="btn in msg.buttons">
                                        <a :href="btn.url" class="px-3 py-1.5 bg-[#d4af37] hover:bg-yellow-600 text-[#121619] font-bold rounded-lg text-[9px] uppercase tracking-wider transition shadow-sm" x-text="btn.label"></a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Indicador de Escribiendo... -->
                    <div class="flex items-center gap-2 text-gray-400" x-show="typing">
                        <span class="text-[10px] italic">La Tribu está pensando...</span>
                    </div>
                </div>

                <!-- Caja de Entrada -->
                <div class="p-3 bg-white border-t border-gray-100 flex items-center gap-2">
                    <input type="text" 
                           x-model="inputMessage" 
                           @keydown.enter="sendMessage()" 
                           placeholder="Pregúntame sobre el menú, reservas..." 
                           class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#d4af37] focus:border-[#d4af37]">
                    <button @click="sendMessage()" class="bg-[#d4af37] hover:bg-yellow-600 text-[#121619] p-2.5 rounded-xl transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#121619]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <script>
            function tribuChatbot() {
                return {
                    open: false,
                    unread: true,
                    typing: false,
                    inputMessage: '',
                    messages: [
                        {
                            sender: 'bot',
                            text: '¡Hola! 🤠 Soy el asistente inteligente de **La Tribu**. Estoy aquí para guiarte en el uso de nuestra plataforma y resolver cualquier duda.\n\n¿Te gustaría saber cómo realizar un pedido, reservar una mesa o conocer las funcionalidades de nuestro software?',
                            buttons: [
                                { label: 'Iniciar Sesión', url: '/login' },
                                { label: 'Registrarse', url: '/register' }
                            ]
                        }
                    ],
                    toggleChat() {
                        this.open = !this.open;
                        if (this.open) {
                            this.unread = false;
                            this.scrollToBottom();
                        }
                    },
                    sendMessage() {
                        if (this.inputMessage.trim() === '') return;
                        const userText = this.inputMessage.trim();
                        this.messages.push({ sender: 'user', text: userText });
                        this.inputMessage = '';
                        this.scrollToBottom();

                        this.typing = true;
                        
                        setTimeout(() => {
                            this.typing = false;
                            const reply = this.getBotReply(userText);
                            this.messages.push(reply);
                            this.scrollToBottom();
                        }, 800);
                    },
                    getBotReply(query) {
                        const q = query.toLowerCase();

                        // Match pedir, ordenar, delivery, etc.
                        if (q.includes('pedir') || q.includes('ordenar') || q.includes('comida') || q.includes('domicilio') || q.includes('llevar') || q.includes('menu') || q.includes('menú') || q.includes('comprar')) {
                            return {
                                sender: 'bot',
                                text: '🍔 **¡Ordena deliciosos platillos a domicilio!**\n\nEn **La Tribu** puedes consultar nuestra carta digital completa con fotos de alta calidad, precios y stock disponible en tiempo real.\n\nPara realizar una compra, debes **Iniciar Sesión** o **Registrarte**. Una vez dentro de tu cuenta de cliente, entra a **"Ver Menú"**, agrega tus productos favoritos al carrito, ingresa tu dirección/teléfono y ¡listo! Se generará tu pedido y tu factura POS de inmediato.',
                                buttons: [
                                    { label: 'Iniciar Sesión', url: '/login' },
                                    { label: 'Registrarse', url: '/register' }
                                ]
                            };
                        }

                        // Match reserva, mesa, fecha, etc.
                        if (q.includes('reserva') || q.includes('reservar') || q.includes('mesa') || q.includes('fecha') || q.includes('hora') || q.includes('agendar')) {
                            return {
                                sender: 'bot',
                                text: '📅 **¡Reserva tu mesa preferida!**\n\nPara asegurar un espacio exclusivo en nuestro salón virtual, solo debes contar con una cuenta de cliente activa. \n\nTe invitamos a **Registrarte** o **Iniciar Sesión**. Luego, dirígete a **"Mis Reservas"**, selecciona una de las mesas registradas por el administrador, indica la fecha, hora e invitados y confirma. Se generará un comprobante de reserva al instante.',
                                buttons: [
                                    { label: 'Iniciar Sesión', url: '/login' },
                                    { label: 'Registrarse', url: '/register' }
                                ]
                            };
                        }

                        // Match roles, sistema, software
                        if (q.includes('roles') || q.includes('rol') || q.includes('sistema') || q.includes('software') || q.includes('mesero') || q.includes('administrador') || q.includes('admin') || q.includes('cliente') || q.includes('funcion')) {
                            return {
                                sender: 'bot',
                                text: '💻 **El software de La Tribu cuenta con 3 roles principales:**\n\n1. **Administrador:** Registra categorías, productos con fotos, mesas, gestiona el stock de inventario y audita los reportes de ventas.\n2. **Mesero:** Administra el estado de las mesas en tiempo real (Disponible, Ocupada, Cuenta, etc.), toma comandas y gestiona las comandas activas.\n3. **Cliente:** Realiza reservas virtuales de mesa, ordena comidas a domicilio y consulta su historial con descargas de facturas POS.',
                                buttons: [
                                    { label: 'Ingresar al Portal', url: '/login' }
                                ]
                            };
                        }

                        // Match factura, pos, pdf
                        if (q.includes('factura') || q.includes('pdf') || q.includes('pos') || q.includes('ticket') || q.includes('cobro') || q.includes('comprobante')) {
                            return {
                                sender: 'bot',
                                text: '🧾 **Facturación Inteligente & Tickets POS PDF:**\n\nCada vez que realizas un pedido o reservas una mesa, el sistema genera de forma automática una factura en la base de datos con el detalle de los productos y montos.\n\nDesde el portal de cliente, en la sección **"Mis Facturas"** o **"Mis Pedidos"**, podrás consultar cualquier consumo y descargar tu tirilla de caja en formato PDF (estilo POS de 80mm) con un solo clic.',
                                buttons: [
                                    { label: 'Ver Facturas', url: '/login' }
                                ]
                            };
                        }

                        // Default fallback
                        return {
                            sender: 'bot',
                            text: '🤠 Estoy a tu servicio. Puedes consultarme acerca de:\n\n• ¿Cómo hacer un **pedido a domicilio**?\n• ¿Cómo **reservar una mesa** virtual?\n• ¿Qué **roles** operan en el software?\n• **Facturas y comprobantes** en formato POS PDF.\n\nSi deseas comenzar a ordenar o agendar una reserva, por favor inicia sesión o crea una cuenta gratis.',
                            buttons: [
                                { label: 'Iniciar Sesión', url: '/login' },
                                { label: 'Registrarse', url: '/register' }
                            ]
                        };
                    },
                    scrollToBottom() {
                        setTimeout(() => {
                            const container = document.getElementById('chat-messages-container');
                            if (container) {
                                container.scrollTop = container.scrollHeight;
                            }
                        }, 50);
                    }
                }
            }
        </script>

    </body>
</html>
