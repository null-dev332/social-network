import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"Segoe UI"',
                    'Roboto',
                    'Helvetica',
                    'Arial',
                    ...defaultTheme.fontFamily.sans,
                ],
                logo: ['"Grand Hotel"', 'cursive'],
            },
            colors: {
                primary: {
                    DEFAULT: '#0095f6',
                    dark: '#1877f2',
                },
                like: '#ff3040',
            },
        },
    },

    plugins: [forms],
};
