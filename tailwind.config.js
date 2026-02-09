import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                slate: {
                    '750': '#1e293b',
                    '800': '#1e293b',
                    '850': '#0f172a',
                    '900': '#0f172a',
                    '950': '#020617',
                },
                accent: {
                    emerald: '#10b981',
                    amber: '#f59e0b',
                    indigo: '#4f46e5',
                }
            }
        },
    },

    plugins: [forms],
};
