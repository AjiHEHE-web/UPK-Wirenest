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
                display: ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                botanical: {
                    50: '#f0f5ec',
                    100: '#e4eddc',
                    200: '#d1dfc8',
                    300: '#b4c9aa',
                    400: '#8dad82',
                    500: '#5b9a55',
                    600: '#34873a',
                    700: '#2b7130',
                    800: '#245d29',
                    900: '#1c4921',
                    950: '#1c281e',
                },
                cream: '#fbfbf6',
                matcha: '#f0f5ec',
                ink: '#1c281e',
                line: '#dfe5d9',
                ochre: '#a8942f',
                sage: '#768473',
            },
        },
    },

    plugins: [forms],
};
