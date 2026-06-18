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
                sans: ['"Open Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'prabhu-red': {
                    50: '#fef2f2',
                    100: '#fde3e3',
                    200: '#fccccc',
                    300: '#f9a8a8',
                    400: '#f47575',
                    500: '#e84a4a',
                    600: '#ce181e',
                    700: '#b0151a',
                    800: '#921418',
                    900: '#79161a',
                },
                'prabhu-dark': '#414042',
                'prabhu-darker': '#303030',
            },
        },
    },

    plugins: [forms],
};
