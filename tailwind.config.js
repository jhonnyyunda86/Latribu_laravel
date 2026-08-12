import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
            },
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
        },
    },

    plugins: [forms],
};
