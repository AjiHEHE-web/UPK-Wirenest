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
                    50: '#f1f6f1',
                    100: '#e1ece1',
                    200: '#c4dac7',
                    300: '#9bbfa3',
                    400: '#6f9c7d',
                    500: '#4d7b60',
                    600: '#385f49',
                    700: '#2c4c3b',
                    800: '#243e32',
                    900: '#1e342a',
                    950: '#16271f',
                },
                cream: '#f8f7f1',
                ink: '#22312a',
                line: '#e7e9e2',
            },
        },
    },

    plugins: [forms],
};
