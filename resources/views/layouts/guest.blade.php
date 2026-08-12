<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'La Tribu') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
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
            body {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="bg-[#FAF4EB] text-[#2c1d11] antialiased flex items-center justify-center min-h-screen p-4 md:p-8">
        
        <!-- Contenedor de la Tarjeta de Doble Cara (Rediseñada para Coherencia con Welcome) -->
        <div class="relative w-full max-w-4xl bg-white border border-gray-200/40 rounded-[2rem] overflow-hidden shadow-2xl grid grid-cols-1 md:grid-cols-2">
            
            {{ $slot }}

        </div>
    </body>
</html>
